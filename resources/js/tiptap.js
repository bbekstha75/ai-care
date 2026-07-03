import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

export default (initialContent) => {
    let editor;

    return {
        content: initialContent || '',
        updatingFromEditor: false,
        tick: 0,

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: [StarterKit],
                content: this.content,
                editorProps: {
                    attributes: {
                        class: 'prose prose-sm dark:prose-invert max-w-none',
                    },
                },
                onUpdate: ({ editor: e }) => {
                    this.updatingFromEditor = true;
                    this.content = e.getHTML();
                    this.tick++;
                    this.$nextTick(() => {
                        this.updatingFromEditor = false;
                    });
                },
                onSelectionUpdate: () => {
                    this.tick++;
                },
                onTransaction: () => {
                    this.tick++;
                },
            });

            this.$watch('content', (value) => {
                if (this.updatingFromEditor) return;
                const newValue = value || '';
                if (editor && newValue !== editor.getHTML()) {
                    editor.commands.setContent(newValue, false);
                }
            });
        },

        isActive(type, attrs = {}) {
            return editor?.isActive(type, attrs) ?? false;
        },

        toggleBold() { editor?.chain().focus().toggleBold().run(); },
        toggleItalic() { editor?.chain().focus().toggleItalic().run(); },
        toggleStrike() { editor?.chain().focus().toggleStrike().run(); },
        toggleCode() { editor?.chain().focus().toggleCode().run(); },
        toggleHeading(level) { editor?.chain().focus().toggleHeading({ level }).run(); },
        toggleBulletList() { editor?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { editor?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote() { editor?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock() { editor?.chain().focus().toggleCodeBlock().run(); },
        setHorizontalRule() { editor?.chain().focus().setHorizontalRule().run(); },
        undo() { editor?.chain().focus().undo().run(); },
        redo() { editor?.chain().focus().redo().run(); },
        canUndo() { return editor?.can().undo() ?? false; },
        canRedo() { return editor?.can().redo() ?? false; },

        destroy() {
            editor?.destroy();
            editor = null;
        },
    };
};
