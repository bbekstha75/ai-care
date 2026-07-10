@props([
    'caseNumber' => 1,
    'title' => '',
    'resultModel' => '',
    'feedbackModel' => '',
    'commentModel' => '',
])

<flux:card class="p-3" {{ $attributes }}>
	<div class="space-y-4" x-data="{ feedback: @entangle($feedbackModel) }">
		{{-- Title & Enhanced Result --}}
		<div>
			<flux:heading size="sm" class="font-black text-sm uppercase">{{ $title }}</flux:heading>
			<div class="flex items-center gap-1.5 mt-1">
				<flux:icon.information-circle variant="solid" class="size-5 text-primary"/>
				<span class="text-xs font-medium text-primary uppercase">{{ __('ENHANCED RESULT') }}</span>
			</div>
		</div>

		{{-- Result Output --}}
		<div
			wire:stream="{{ $resultModel }}"
			class="w-full min-h-60 p-4 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-y-auto prose prose-sm prose-zinc dark:prose-invert max-w-none"
		>
			<x-markdown>
				{!! data_get($this, $resultModel) !!}
			</x-markdown>
		</div>

		{{-- Action Buttons --}}
		<div class="flex flex-wrap items-center gap-3">
			<flux:button
				variant="outline"
				size="sm"
				class="text-xs disabled:text-gray-300 bg-custom-sky! text-white!"
				icon="clipboard-document"
				wire:click="copyToClipboard({{ $caseNumber }})"
			>
				{{ __('COPY TO CLIPBOARD') }}
			</flux:button>

			<flux:button
				variant="outline"
				size="sm"
				class="text-xs disabled:text-gray-300 bg-custom-sky! text-white!"
				icon="plus"
				wire:click="saveVisitCasenote({{ $caseNumber }})"
			>
				{{ __('SAVE A VISIT CASENOTE') }}
			</flux:button>

			<div class="flex items-center gap-2 ml-2">
				<span class="text-xs text-[#14bef0] uppercase font-semibold">{{ __('ACCURATE?') }}</span>

				{{-- Thumbs Up: outline when inactive, solid when active --}}
				<flux:button
					variant="ghost"
					size="sm"
					icon:variant="outline"
					icon="hand-thumb-up"
					x-show="feedback !== 'up'"
					x-on:click="feedback = 'up'; $wire.submitFeedback({{ $caseNumber }}, 'up')"
					class="text-emerald-600! hover:text-emerald-500 dark:text-zinc-400 dark:hover:text-emerald-400"
				/>
				<flux:button
					variant="ghost"
					size="sm"
					icon:variant="solid"
					icon="hand-thumb-up"
					x-show="feedback === 'up'"
					x-on:click="feedback = null; $wire.submitFeedback({{ $caseNumber }}, null)"
					x-cloak
					class="text-emerald-600! hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300"
				/>

				{{-- Thumbs Down: outline when inactive, solid when active --}}
				<flux:button
					variant="ghost"
					size="sm"
					icon:variant="outline"
					icon="hand-thumb-down"
					x-show="feedback !== 'down'"
					x-on:click="feedback = 'down'; $wire.submitFeedback({{ $caseNumber }}, 'down')"
					class="text-red-600! hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400"
				/>
				<flux:button
					variant="ghost"
					size="sm"
					icon:variant="solid"
					icon="hand-thumb-down"
					x-show="feedback === 'down'"
					x-on:click="feedback = null; $wire.submitFeedback({{ $caseNumber }}, null)"
					x-cloak
					class="text-red-600! hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
				/>
			</div>
		</div>

		{{-- Additional Comments (shown only when feedback is active) --}}
		<div x-show="feedback !== null" x-transition.opacity.duration.200ms class="flex items-center gap-3">
			<flux:input
				wire:model="{{ $commentModel }}"
				placeholder="{{ __('Any additional comments?') }}"
				class="flex-1"
			/>
			<flux:button
				variant="outline"
				size="sm"
				icon="plus"
				wire:click="saveFeedbackComment({{ $caseNumber }})"
			>
				{{ __('SAVE') }}
			</flux:button>
		</div>
	</div>
</flux:card>

