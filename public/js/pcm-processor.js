/**
 * AudioWorkletProcessor to downsample and convert float32 audio to Int16 PCM.
 * AssemblyAI's streaming WebSocket requires 16kHz PCM16 audio.
 */
class PCMProcessor extends AudioWorkletProcessor {
    constructor() {
        super();
        this.targetSampleRate = 16000;
        this.buffer = new Int16Array(this.targetSampleRate);
        this.bufferLength = 0;
        this.chunkSize = 4096;
    }

    process(inputs, outputs, parameters) {
        const input = inputs[0];
        if (!input || !input[0]) return true;

        const inputChannel = input[0];

        const ratio = sampleRate / this.targetSampleRate;

        let inputOffset = 0;
        while (inputOffset < inputChannel.length) {
            const s = Math.max(-1, Math.min(1, inputChannel[Math.floor(inputOffset)]));
            this.buffer[this.bufferLength++] = s < 0 ? s * 0x8000 : s * 0x7FFF;

            if (this.bufferLength >= this.chunkSize) {
                const chunk = this.buffer.slice(0, this.bufferLength);
                this.port.postMessage(chunk.buffer, [chunk.buffer]);
                this.bufferLength = 0;
            }

            inputOffset += ratio;
        }

        return true;
    }
}

registerProcessor('pcm-processor', PCMProcessor);
