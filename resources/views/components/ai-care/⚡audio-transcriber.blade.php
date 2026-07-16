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

            $this->dispatch('transcription-complete', transcript: $this->transcript);

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

	<template x-if="formattedLiveTranscript">
		<div class="mt-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-mono">
			<div class="text-xs text-gray-500 mb-2 border-b w-fit pb-2 font-sans flex items-center gap-2">
				Live Transcription Preview
			</div>
			<div x-text="formattedLiveTranscript"></div>
		</div>
	</template>

	<div class="flex items-center gap-4">
		<flux:button
			x-on:click="recording ? stop() : start()"
			size="sm"
			variant="primary"
			icon="audio-line"
			class="bg-primary! hover:bg-primary/80! text-white! mb-0">
			<span x-text="recording ? 'STOP RECORDING' : 'START CONSULTATION'"></span>
		</flux:button>

		<div x-show="recording" style="display: none;" class="flex items-center gap-2 text-sm text-red-500 font-medium">
			<div class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></div>
			<span x-text="elapsed"></span>
		</div>
	</div>

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

			// Streaming properties
			audioContext: null,
			ws: null,
			liveTranscript: '',
			turns: {}, // Store finalized turns by turn_order

			get formattedLiveTranscript() {
				// Combine finalized turns and the current live transcript
				const finalized = Object.values(this.turns).map(t => `Speaker ${t.speaker_label || 'UNKNOWN'}: ${t.transcript}`).join('\n');
				return finalized + (finalized && this.liveTranscript ? '\n' : '') + this.liveTranscript;
			},

			async start () {
				if ($wire.speakerDiarization) {
					await this.startStreaming();
				} else {
					await this.startBatch();
				}
			},

			async startBatch () {
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

			async startStreaming () {
				try {
					// 1. Fetch token
					const tokenRes = await fetch('/api/assemblyai/token');
					if (!tokenRes.ok) throw new Error('Failed to fetch AssemblyAI token');
					const { token } = await tokenRes.json();

					// 2. Open WebSocket
					const params = new URLSearchParams({
						sample_rate: 16000,
						speech_model: 'universal-3-5-pro',
						speaker_labels: 'true',
						max_speakers: 2,
						domain: 'medical-v1',
						min_turn_silence: 800,
						max_turn_silence: 3600,
						token: token
					});

					this.ws = new WebSocket(`wss://streaming.assemblyai.com/v3/ws?${params.toString()}`);
					this.turns = {};
					this.liveTranscript = '';

					this.ws.onmessage = (event) => {
						const msg = JSON.parse(event.data);
						if (msg.type === 'Turn') {
							if (msg.end_of_turn) {
								this.turns[msg.turn_order] = {
									speaker_label: msg.speaker_label,
									transcript: msg.transcript
								};
								this.liveTranscript = '';
							} else {
								const speaker = msg.speaker_label || 'UNKNOWN';
								this.liveTranscript = `Speaker ${speaker}: ${msg.transcript}`;
							}
						} else if (msg.type === 'SpeakerRevision') {
							for (const revision of msg.revisions || []) {
								if (this.turns[revision.turn_order]) {
									this.turns[revision.turn_order].speaker_label = revision.speaker_label;
								}
							}
						}
					};

					// 3. Start AudioWorklet
					this.audioContext = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: 16000 });
					await this.audioContext.audioWorklet.addModule('/js/pcm-processor.js');

					const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
					const source = this.audioContext.createMediaStreamSource(stream);
					const processor = new AudioWorkletNode(this.audioContext, 'pcm-processor');

					processor.port.onmessage = (event) => {
						if (this.ws && this.ws.readyState === WebSocket.OPEN) {
							this.ws.send(event.data);
						}
					};

					source.connect(processor);
					processor.connect(this.audioContext.destination);

					// Store stream references to stop them later
					this.mediaStream = stream;
					this.processor = processor;

					this.recording = true;
					this.startTimer();

				} catch (e) {
					console.error(e);
					alert('Could not start streaming transcription.');
				}
			},

			stop () {
				if (!this.recording) return;

				if ($wire.speakerDiarization) {
					this.stopStreaming();
				} else {
					if (this.mediaRecorder) this.mediaRecorder.stop();
				}

				this.recording = false;
				this.stopTimer();
			},

			stopStreaming () {
				if (this.processor) this.processor.disconnect();
				if (this.mediaStream) this.mediaStream.getTracks().forEach(t => t.stop());
				if (this.audioContext) this.audioContext.close();

				if (this.ws && this.ws.readyState === WebSocket.OPEN) {
					this.ws.send(JSON.stringify({ type: 'Terminate' }));
					// Wait a moment for final messages before closing
					setTimeout(() => {
						this.ws.close();
						const finalTranscript = this.formattedLiveTranscript;
						$wire.set('transcript', finalTranscript);
						$wire.dispatch('transcription-complete', { transcript: finalTranscript });
					}, 1000);
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
