# AICare v3 — Rebuild Plan: Laravel 13 + Livewire 4 + Laravel AI SDK

## Context

The current AICare app (Laravel 10 + Vue/Inertia) is a medical documentation assistant whose
core is a **1,518-line `AiController`** with hardcoded models, prompts embedded as PHP strings,
synchronous blocking transcription, no tests, and no tenancy story. The two attached documents
(`src/imports/aicare_project_analysis.md`, `src/imports/implementation_plan.md`) capture the full
domain and a prior Laravel 12/Livewire 3 proposal.

This plan **supersedes** that proposal and targets the current stable stack (verified against 2026
docs):

- **Laravel 13** (released 2026-03-17, PHP 8.3+) — stable first-party **AI SDK** (`laravel/ai`),
  native PHP attributes, pgvector semantic search, Reverb DB driver, team-based starter kits.
- **Livewire 4** — single-file components (Volt merged in), **Islands** (isolated partial
  re-renders — this is what "livewire pool" refers to), `wire:stream`, `wire:sort`, Blaze compiler.
- **Laravel AI SDK** (`laravel/ai`, built on Prism) — Agent classes, tool-calling, SSE streaming,
  structured output via `JsonSchema`, DB-backed conversation persistence, embeddings, test fakes.

**Confirmed decisions:**
- AI layer: **official `laravel/ai` SDK** (not raw openai-php, not Prism directly).
- Prompts: **admin-editable, stored in DB** (`prompt_templates` table), rendered at runtime.
- Tenancy: **not built now**, but schema/models designed tenancy-ready — nullable
  `organization_id` + a `BelongsToOrganization` trait/global-scope scaffold added now, enforcement
  deferred. This avoids a painful rewrite later without over-building today.

Intended outcome: a clean, testable, domain-oriented rebuild where the AI generation flow is driven
by the Laravel AI SDK, prompts live in the DB, and the streaming UI uses Livewire 4 Islands.

---

## Reference Docs (read before implementing)

- Laravel 13 releases: https://laravel.com/docs/13.x/releases
- Laravel AI SDK guide: https://richdynamix.com/articles/laravel-ai-sdk-complete-guide
- Livewire 4 Islands: https://livewire.laravel.com/docs/4.x/islands
- Livewire 4 overview: https://laravel-news.com/everything-new-in-livewire-4

---

## Target Architecture

Domain-oriented layout under `app/`, thin controllers, Action classes, provider abstraction handled
by the AI SDK itself.

```
app/
├── Ai/
│   ├── Agents/
│   │   ├── ClinicalNoteAgent.php     # #[Provider]/#[Model]/#[MaxTokens], Promptable, RemembersConversations
│   │   └── FileChatAgent.php          # tool-enabled agent for chat-with-files (HasTools, #[MaxSteps(5)])
│   ├── Actions/
│   │   ├── GenerateClinicalNote.php   # limits → prompt build → history record → stream
│   │   └── UpdateAiSettings.php
│   ├── Enums/PromptType.php           # dx, clinical_plan, medical_scribe, ... , chat_with_files
│   ├── Prompts/PromptBuilder.php      # loads prompt_templates row, renders Blade string w/ vars
│   ├── Tools/SimilaritySearchTool.php # optional RAG over diagnoses/docs (pgvector)
│   └── Events/{AiGenerationStarted,Completed,Failed}.php
├── Transcription/
│   ├── Contracts/TranscriptionProvider.php
│   ├── Providers/{WhisperProvider,AssemblyAiProvider}.php
│   └── Jobs/ProcessTranscription.php  # QUEUED (removes 2-min blocking)
├── Subscription/Services/UsageLimiter.php   # monthly count vs plan; used by Gate + middleware
├── Support/Concerns/BelongsToOrganization.php  # tenancy-ready scaffold (nullable org_id + scope)
└── Livewire/
    ├── Ai/AiCarePage.php              # full-page SFC; input, prompt selector, settings island
    ├── CaseManagement/{CaseNotesIndex,GenerationHistoriesIndex,CaseTemplates}.php
    └── Admin/{Users,Roles,SubscriptionPlans,PromptTemplateEditor,AppSettings}.php
```

**Why the AI SDK collapses the old design:** provider strategy, streaming, failover, retries, and
conversation persistence are all built in. We do NOT rebuild `AiProviderInterface`/`OpenAiProvider`;
we configure agents and rely on `provider: [Lab::Anthropic, Lab::OpenAI]` failover and
`#[UseCheapestModel]`/`#[UseSmartestModel]` instead of hardcoding `gpt-5.4`.

---

## Database Schema (key changes vs current)

Adopt the improved schema from the prior proposal (`implementation_plan.md` Part 4) with these
concrete decisions:

- `generation_histories` (renamed from `case_note_histories`): add `model_used`, `token_count`,
  `duration_ms`, `metadata` json, `review_status` enum, `reviewer_comments`; keep soft deletes;
  **index** `user_id`, `prompt_type`, `created_at`.
- `case_notes`: replace `title IS NULL` draft convention with `status` enum (`draft|saved|archived`), indexed.
- `prompt_templates` (**NEW**, admin-managed): `prompt_type` (unique, enum-backed), `name`,
  `system_prompt` longText (Blade placeholders `{{ $input }}`, `{{ $language }}`),
  `styling_instructions` longText, `is_active`, `version`, timestamps.
- `subscription_plans` (renamed): `allowed_prompts` json, `max_generations_per_month`,
  `allow_file_transcription`, `allow_audio_transcription`, `features` json.
- `ai_settings`: add `transcription_provider` enum (`whisper|assemblyai`), default model `gpt-4o`.
- `app_settings`: key-value pattern (replaces single-row `settings`), cached.
- `transcription_jobs` (**NEW**): async status tracking (`pending|processing|completed|failed`).
- **Tenancy-ready**: add nullable `organization_id` to `users`, `case_notes`,
  `generation_histories`, `case_templates`, `subscription_plans`, `app_settings`. No enforcement
  yet — column + index only, plus the `BelongsToOrganization` trait wired but scope disabled by a
  config flag.
- Use the AI SDK migrations for `agent_conversations` + `agent_conversation_messages`
  (`RemembersConversations`) instead of hand-rolling chat history.
- Optional pgvector `embedding` column on `diagnoses` for `SimilaritySearch` (Postgres) — gate
  behind a config flag if staying on MySQL.

Enums to create: `PromptType`, `CaseNoteStatus`, `ReviewStatus`, `TranscriptionStatus`,
`TranscriptionProvider`.

---

## AI Generation Flow (the core feature)

1. **`AiCarePage`** (Livewire 4 single-file component): text input, `PromptType` selector, case
   template + saved-note dropdowns, AI-settings **Island** (model/language — re-renders in isolation).
2. On generate → `GenerateClinicalNote` action:
    - `UsageLimiter->enforceOrFail($user)` (Gate + `throttle:ai` middleware on the route).
    - `PromptBuilder->build($promptType, $input, $language)` — loads the active `prompt_templates`
      row and renders it (admin-editable, versioned).
    - Create `generation_histories` row (`output` filled on completion); dispatch `AiGenerationStarted`.
    - Stream via the agent: `(new ClinicalNoteAgent)->forUser($user)->stream($prompt)`.
3. **Streaming UI** uses **Livewire 4 `wire:stream`** inside an **Island** so only the output region
   re-renders. Iterate SSE events, append text, persist final `output` + token/duration on completion
   (replaces the old raw-SSE-in-controller + `<END_STREAMING_SSE>` sentinel).
4. **Chat-with-files**: `FileChatAgent` (`HasTools`, `#[MaxSteps(5)]`) using the SDK's file/vector
   store support — and a cleanup path on component teardown (fixes the old resource-leak bug).
5. **Transcription** (Whisper / AssemblyAI): audio upload → `ProcessTranscription` **queued job** →
   status surfaced via Livewire polling Island (or Reverb DB driver for instant updates).

---

## Critical Files to Create / Modify

- Migrations: all tables above, in dependency order (`medical_experiences` → `users` → permission
  tables → `subscription_plans` → `ai_settings` → `generation_histories` → `case_notes` →
  `case_templates` → `prompt_templates` → `diagnoses` → `app_settings` → `transcription_jobs`).
- Models with enum/json casts, relationships, scopes (`scopeForUser`, `scopeActive`), and
  `BelongsToOrganization`.
- `app/Ai/Agents/ClinicalNoteAgent.php`, `FileChatAgent.php`.
- `app/Ai/Actions/GenerateClinicalNote.php`, `app/Ai/Prompts/PromptBuilder.php`.
- `app/Ai/Enums/PromptType.php` + other enums.
- `app/Transcription/**` (contract, 2 providers, queued job).
- `app/Subscription/Services/UsageLimiter.php` + `EnsureWithinUsageLimits` middleware + Gate.
- Livewire 4 components under `app/Livewire/**` + Blade views (single-file where practical).
- Seeders: `PromptTemplateSeeder` (extract all 10 prompts from old code, splitting medical
  instructions from CSS/styling), `SubscriptionPlanSeeder`, `MedicalExperienceSeeder`,
  `DiagnosisSeeder`, `AppSettingsSeeder`, `RoleAndPermissionSeeder`, `AdminUserSeeder`.
- Config: `config/ai.php` (providers, failover order, default models), `.env.example` with
  **placeholder** keys only (fixes the leaked-keys flaw).

---

## Phased Delivery

1. **Scaffold** — `laravel new aicare` (Laravel 13), Livewire 4, `composer require laravel/ai`
   (+ publish/migrate), `spatie/laravel-permission`, Pest, queue driver. Placeholder `.env.example`.
2. **DB & models** — migrations, enums, models, seeders (incl. `PromptTemplateSeeder`).
3. **AI domain** — agents, `PromptBuilder`, `GenerateClinicalNote`, events, `UsageLimiter`.
4. **Transcription domain** — providers + queued `ProcessTranscription` + status tracking.
5. **Auth & layout** — starter-kit auth (extend register with medical fields), role-based nav.
6. **AICare page** ★ — Livewire 4 SFC + Islands + `wire:stream` output + audio recorder + file chat.
7. **Case management** — case notes, generation histories + review, templates.
8. **Admin** — users, roles, subscription plans, **prompt template editor** (DB-driven), app settings.
9. **Hardening** — `throttle:ai`, structured logging, caching (settings/diagnoses), Pulse, indexes,
   file-upload validation, prompt-injection mitigation, security audit.

---

## Verification

- `php artisan test` (Pest): unit tests for `PromptBuilder`, `UsageLimiter`, enums; feature tests for
  auth, generation (using AI SDK `Agent::fake()` + `assertPrompted()`, `->preventStrayPrompts()` in
  CI), subscription enforcement, case-note CRUD, role/permission guards.
- Livewire component tests for `AiCarePage` (submission/validation) and admin CRUD.
- Manual: run generation end-to-end, confirm streamed output renders in the Island, `generation_histories`
  row is written with `model_used`/`token_count`/`duration_ms`, and admin prompt edits take effect
  without a deploy.
- Confirm queued transcription completes without blocking a web worker.

---

## Open Items (confirm during build)

- **Tenancy timing**: columns/scaffold added now; enforce global scope + switch to team-based
  starter kit when tenancy work actually begins.
- **DB engine for vector search**: pgvector needs Postgres; if MySQL is required, gate embeddings
  behind a flag and skip `SimilaritySearch`.
- **Rich text editor**: TipTap (lighter, Alpine-friendly) vs CKEditor 5 — default TipTap.
- **Transcription realtime**: Livewire polling (simple) vs Reverb DB driver (instant) — default polling.
- **Data migration**: assume fresh start unless existing MySQL data must be carried over.
