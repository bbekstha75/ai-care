@props([
    'label' => null,
    'description' => null,
])

<flux:field>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        x-data="tiptap(@entangle($attributes->wire('model')))"
        wire:ignore
        class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden bg-white dark:bg-zinc-900 mt-2"
    >
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-0.5 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-1.5 py-1">
            {{-- Bold --}}
            <button type="button" @click.prevent="toggleBold()" :class="(tick, isActive('bold')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Bold (Ctrl+B)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
            </button>

            {{-- Italic --}}
            <button type="button" @click.prevent="toggleItalic()" :class="(tick, isActive('italic')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Italic (Ctrl+I)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
            </button>

            {{-- Strikethrough --}}
            <button type="button" @click.prevent="toggleStrike()" :class="(tick, isActive('strike')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Strikethrough">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M16 4H9a3 3 0 0 0-3 3c0 1.4.8 2.6 2 3.2"/><path d="M4 12h16"/><path d="M15 12c1.2.6 2 1.8 2 3.2a3 3 0 0 1-3 3H8"/></svg>
            </button>

            {{-- Inline Code --}}
            <button type="button" @click.prevent="toggleCode()" :class="(tick, isActive('code')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Inline Code">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </button>

            <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-600 mx-0.5"></div>

            {{-- Heading 2 --}}
            <button type="button" @click.prevent="toggleHeading(2)" :class="(tick, isActive('heading', { level: 2 })) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="px-1.5 py-1 rounded text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Heading 2">
                H2
            </button>

            {{-- Heading 3 --}}
            <button type="button" @click.prevent="toggleHeading(3)" :class="(tick, isActive('heading', { level: 3 })) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="px-1.5 py-1 rounded text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Heading 3">
                H3
            </button>

            <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-600 mx-0.5"></div>

            {{-- Bullet List --}}
            <button type="button" @click.prevent="toggleBulletList()" :class="(tick, isActive('bulletList')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Bullet List">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
            </button>

            {{-- Ordered List --}}
            <button type="button" @click.prevent="toggleOrderedList()" :class="(tick, isActive('orderedList')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Ordered List">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><text x="2" y="8" fill="currentColor" stroke="none" font-size="7" font-weight="600">1</text><text x="2" y="14" fill="currentColor" stroke="none" font-size="7" font-weight="600">2</text><text x="2" y="20" fill="currentColor" stroke="none" font-size="7" font-weight="600">3</text></svg>
            </button>

            {{-- Blockquote --}}
            <button type="button" @click.prevent="toggleBlockquote()" :class="(tick, isActive('blockquote')) ? 'bg-zinc-200 dark:bg-zinc-600 text-zinc-900 dark:text-white' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Blockquote">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C9.591 11.69 11 13.19 11 15c0 1.866-1.363 3.5-3.5 3.5-1.073 0-2.083-.483-2.917-1.179zM14.583 17.321C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C19.591 11.69 21 13.19 21 15c0 1.866-1.363 3.5-3.5 3.5-1.073 0-2.083-.483-2.917-1.179z"/></svg>
            </button>

            <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-600 mx-0.5"></div>

            {{-- Horizontal Rule --}}
            <button type="button" @click.prevent="setHorizontalRule()" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Horizontal Rule">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="size-4"><line x1="3" y1="12" x2="21" y2="12"/></svg>
            </button>

            <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-600 mx-0.5"></div>

            {{-- Undo --}}
            <button type="button" @click.prevent="undo()" :class="(tick, !canUndo()) ? 'opacity-40 cursor-not-allowed' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Undo (Ctrl+Z)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
            </button>

            {{-- Redo --}}
            <button type="button" @click.prevent="redo()" :class="(tick, !canRedo()) ? 'opacity-40 cursor-not-allowed' : ''" class="p-1.5 rounded text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-white transition" title="Redo (Ctrl+Shift+Z)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.13-9.36L23 10"/></svg>
            </button>
        </div>

        {{-- Editor Content --}}
        <div x-ref="editor" class="[&_.ProseMirror]:min-h-[150px] [&_.ProseMirror]:px-3 [&_.ProseMirror]:py-2 [&_.ProseMirror]:outline-none"></div>
    </div>

    @if ($description)
        <flux:description>{{ $description }}</flux:description>
    @endif

    <flux:error :name="$attributes->wire('model')->value()" />
</flux:field>
