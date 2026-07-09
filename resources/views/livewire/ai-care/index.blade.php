<div class="space-y-6" x-data="{ showCaseTwo: false }"
     x-on:copy-to-clipboard.window="
         navigator.clipboard.writeText($event.detail.text).then(() => {
             $flux.toast('Copied to clipboard!');
         })
     "
>
	<x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('AI Care')],
    ]"/>

	{{-- Header Description --}}
	<div class="px-1">
		<p class="text-sm text-zinc-700 dark:text-zinc-300">
			<span class="font-semibold text-zinc-900 dark:text-white">{{ __('Docspe AICare') }}</span>
			{{ __('is an AI co-pilot, that works as an AI medical resident, helping you to transcribe patient-doctor conversations into structured notes, analyze clinical cases, and more.') }}
		</p>
	</div>

	<flux:card>
		<div class="space-y-4">
			{{-- Tag History & Physical --}}
			<div>
				<flux:heading size="sm"
				              class="mb-2 font-bold uppercase tracking-wide">{{ __('Tag History & Physical') }}</flux:heading>
				<flux:select size="sm" wire:model="tagHistory" placeholder="{{ __('Choose Tag History') }}">
					<flux:select.option value="history_physical">{{ __('History & Physical') }}</flux:select.option>
					<flux:select.option value="progress_note">{{ __('Progress Note') }}</flux:select.option>
					<flux:select.option value="discharge_summary">{{ __('Discharge Summary') }}</flux:select.option>
					<flux:select.option value="consultation">{{ __('Consultation') }}</flux:select.option>
					<flux:select.option value="operative_note">{{ __('Operative Note') }}</flux:select.option>
				</flux:select>
			</div>

			{{-- Patient Summary --}}
			<div>
				<flux:heading size="sm"
				              class="mb-1 font-bold uppercase tracking-wide">{{ __('Patient Summary') }}</flux:heading>
				<p class="text-xs text-zinc-500 dark:text-zinc-400">
					{{ __('Include age, sex, relevant past medical history, medications, presenting symptoms, vitals associated symptoms, relevant data (labs, imaging, other studies) and illness course.') }}
				</p>
			</div>

			{{-- Speaker Diarization --}}
			<div class="space-y-1">
				<div class="flex items-center gap-3">
					<flux:heading size="sm" class="font-bold">{{ __('Speaker Diarization') }}</flux:heading>
					<flux:switch wire:model="speakerDiarization"/>
				</div>
				<p class="text-xs text-zinc-500 dark:text-zinc-400">
					{{ __('Multi speaker diarization is only available for transcriptions in these languages:') }}
				</p>
				<p class="text-xs text-zinc-500 dark:text-zinc-400">
					{{ __('(English, German, French, Spanish, And...') }}
					<a href="#"
					   class="text-blue-600 dark:text-blue-400 underline">{{ __('See the full list here') }}</a>{{ __(')') }}
					{{ __('We will be adding more languages soon with multi speaker diarization.') }}
				</p>
			</div>

			{{-- Start Consultation Button --}}
			<div>
				<flux:button size="sm" variant="primary" icon="audio-line" wire:click="startConsultation"
				             class="bg-primary! hover:bg-primary/80! text-white!">
					{{ __('START CONSULTATION') }}
				</flux:button>
			</div>

			{{-- Instruction Text --}}
			<div class="flex justify-between items-center">
				<p class="text-xs text-zinc-600 dark:text-zinc-400">
					{{ __('Paste or Dictate Your Patient Summary / Clinical Casenote Here, or') }}
					<a href="#"
					   class="text-blue-600 dark:text-blue-400 underline">{{ __('Alternatively Use Our Clinical Case Template') }}</a>
					{{ __('from The Dropdown Below') }}
				</p>

				<div>
					<flux:button size="sm" class="text-xs" variant="outline" icon="arrow-path" wire:click="resetForm">
						{{ __('RESET') }}
					</flux:button>
				</div>
			</div>


			{{-- Toolbar Row --}}
			<div class="flex  items-center gap-3">
				{{-- Clinical Style Select --}}
				<flux:select
					class="text-xs"
					size="sm"
					wire:model="clinicalStyle"
					placeholder="{{ __('SELECT CLINICAL STYLE') }}"
				>
					<flux:select.option value="soap">{{ __('SOAP Note') }}</flux:select.option>
					<flux:select.option value="h_and_p">{{ __('History & Physical') }}</flux:select.option>
					<flux:select.option value="progress_note">{{ __('Progress Note') }}</flux:select.option>
					<flux:select.option value="discharge_summary">{{ __('Discharge Summary') }}</flux:select.option>
					<flux:select.option value="consultation_note">{{ __('Consultation Note') }}</flux:select.option>
				</flux:select>

				{{-- Attach File --}}
				<flux:button class="text-xs" size="sm" variant="outline" icon="paper-clip">
					{{ __('ATTACH FILE') }}
				</flux:button>

				{{-- Template Select --}}
				<flux:select class="text-xs" size="sm" wire:model="selectedTemplate"
				             placeholder="{{ __('Choose a template') }}">
					@foreach ($templateOptions as $option)
						<flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
					@endforeach
				</flux:select>

				{{-- Transcription Select --}}
				<flux:select class="text-xs" size="sm" wire:model="selectedTranscription"
				             placeholder="{{ __('Choose from transcription') }}">
					<flux:select.option value="">{{ __('Choose from transcription') }}</flux:select.option>
				</flux:select>

			</div>

			{{-- Main Input Textarea --}}
			<flux:textarea
				wire:model="mainInput"
				rows="1"
				placeholder="{{ __('Enter patient summary, clinical notes, or dictate here...') }}"
				class="w-full"
			/>

			{{-- Action Buttons --}}
			<div class="flex flex-wrap items-center gap-3">
				<flux:button
					size="sm"
					wire:click="generateCase"
					wire:loading.attr="disabled"
					wire:target="generateCase"
					icon="sparkles"
					class="text-xs"
				>
					<span wire:loading.remove wire:target="generateCase">{{ __('GENERATE CASE') }}</span>
					<span wire:loading wire:target="generateCase">{{ __('GENERATING...') }}</span>
				</flux:button>

				<flux:button
					size="sm"
					wire:click="generateSecondCase"
					wire:loading.attr="disabled"
					wire:target="generateSecondCase"
					icon="sparkles"
					class="text-xs"
					x-on:click="showCaseTwo = true"
				>
					<span wire:loading.remove wire:target="generateSecondCase">{{ __('GENERATE 2ND CASE') }}</span>
					<span wire:loading wire:target="generateSecondCase">{{ __('GENERATING...') }}</span>
				</flux:button>

				<flux:button
					size="sm"
					wire:click="saveScribe"
					variant="outline"
					icon="plus"
					class="text-xs"
				>
					{{ __('SAVE SCRIBE') }}
				</flux:button>
			</div>
		</div>
	</flux:card>

	{{-- 1ST CASE RESULT --}}
	<x-ai-care.case-result
		:case-number="1"
		:title="__('1ST CASE | STRUCTURED CLINICAL NOTE')"
		result-model="caseOneResult"
		feedback-model="caseOneFeedback"
		comment-model="caseOneComment"
	/>

	{{-- 2ND CASE RESULT --}}
	<x-ai-care.case-result
		:case-number="2"
		:title="__('2ND CASE |')"
		result-model="caseTwoResult"
		feedback-model="caseTwoFeedback"
		comment-model="caseTwoComment"
		x-show="showCaseTwo || @js($caseTwoResult !== '')"
		x-transition.opacity.duration.300ms
	/>

	{{-- Important Disclaimer --}}
	<div
		class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 p-4 space-y-2">
		<flux:heading size="sm" class="font-bold underline">{{ __('Important to read') }}</flux:heading>
		<p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
			<span class="font-semibold">{{ __('AICare') }}</span>
			{{ __('is an AI product, that can be embedded into other healthcare systems. It leverages a foundational large language models and other Advanced LLMs to generate results based on a submitted clinical problem representation. The foundational large language model is combined with context, including evidence-based guidelines, schemas, and case studies, to achieve a high level of clinical excellence in our AI outputs.') }}
		</p>
		<p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
			{{ __('All information entered should be fully de-identified. All direct identifiers of a patient, such') }}
			<span
				class="font-bold">{{ __('name, address, email address, or any other details that could identify an individual, should be removed.') }}</span>
			{{ __('Please review a full list of data elements that should be removed when de-identifying patient information') }}
			<a href="#" class="text-blue-600 dark:text-blue-400 underline">{{ __('here') }}</a>.
		</p>
	</div>
</div>
