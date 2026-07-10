<?php

use Laravel\Ai\Transcription;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|file|mimetypes:audio/webm,video/webm,application/octet-stream,audio/ogg,audio/mpeg,audio/mp4,audio/wav|max:25600')]
    public $audio;

    public string $transcript = '';

    public bool $processing = false;
    public bool $speakerDiarization = false;

    public ?string $error = null;

    /**
     * Called from Alpine once the audio blob has finished uploading.
     */
    public function transcribe(): void
    {
        $this->reset('error');
        $this->validate();

        $this->processing = true;
        $path = $this->audio->getRealPath();

        try {
            $this->transcript = Transcription::fromPath($path)
                ->generate();

            $this->dispatch('transcription-complete', ['transcript' => $this->transcript]);

        } catch (\Throwable $e) {
            report($e);
            $this->error = 'Could not transcribe the recording. Please try again.';
        } finally {
            if (is_file($path)) {
                @unlink($path);
            }
            $this->audio = null;
            $this->processing = false;
        }
    }
};
?>

<div x-data="audioRecorder()" class="space-y-4">
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

	<flux:button
		x-on:click="recording ? stop() : start()"
		size="sm"
		variant="primary"
		icon="audio-line"
		class="bg-primary! hover:bg-primary/80! text-white! mb-0">
		<span x-text="recording ? 'STOP RECORDING' : 'START CONSULTATION'"></span>
	</flux:button>

	<span wire:loading wire:target="audio" class="text-sm text-gray-500">Uploading...</span>
	<span wire:loading wire:target="transcribe" class="text-sm text-gray-500">Transcribing...</span>

	@if ($error)
		<p class="text-sm text-red-600">{{ $error }}</p>
	@endif
	@error('audio') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

	@script
	<script>
			Alpine.data('audioRecorder', () => ({
				recording: false,
				mediaRecorder: null,
				chunks: [],
				elapsed: '0:00',
				timer: null,
				startedAt: null,

				async start () {
					try {
						const stream = await navigator.mediaDevices.getUserMedia({audio: true});
						this.chunks = [];
						this.mediaRecorder = new MediaRecorder(stream, {mimeType: 'audio/webm'});

						this.mediaRecorder.addEventListener('dataavailable', (e) => {
							if (e.data.size > 0) this.chunks.push(e.data);
						});
						this.mediaRecorder.addEventListener('stop', () => this.upload(stream));

						this.mediaRecorder.start();
						this.recording = true;
						this.startTimer();
					} catch (e) {
						alert('Microphone access is required to record audio.');
					}
				},

				stop () {
					if (this.mediaRecorder && this.recording) {
						this.mediaRecorder.stop();
						this.recording = false;
						this.stopTimer();
					}
				},

				upload (stream) {
					stream.getTracks().forEach((t) => t.stop());

					const blob = new Blob(this.chunks, {type: 'audio/webm'});
					const file = new File([blob], `recording-${Date.now()}.webm`, {type: 'audio/webm'});

					$wire.upload('audio', file, () => {
						$wire.transcribe();
					});
				},

				startTimer () {
					this.startedAt = Date.now();
					this.timer = setInterval(() => {
						const s = Math.floor((Date.now() - this.startedAt) / 1000);
						this.elapsed = `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`;
					}, 500);
				},

				stopTimer () {
					clearInterval(this.timer);
				}
			}));
	</script>
	@endscript
</div>
