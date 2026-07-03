import tiptap from './tiptap';

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptap', tiptap);
});
