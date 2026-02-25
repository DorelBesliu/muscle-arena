import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

/**
 * Alpine data for TipTap rich text editor.
 * Usage in Blade: x-data="tiptapEditor(config)"
 */
export function tiptapEditorData(config) {
    let editor;
    return {
        updatedAt: Date.now(),
        _tiptapInitialSyncDone: false,
        init() {
            const el = document.getElementById(config.initialContentId);
            const initialContent = (el && (el.value !== undefined ? el.value : el.textContent)) ? (el.value !== undefined ? el.value : el.textContent).trim() : '';
            const _this = this;
            editor = new Editor({
                element: this.$refs.element,
                extensions: [
                    StarterKit.configure({ link: { openOnClick: false } }),
                    Placeholder.configure({ placeholder: config.placeholder || '…' }),
                ],
                content: initialContent || '',
                editorProps: {
                    attributes: {
                        class: 'tiptap-content min-h-[140px] outline-none',
                    },
                },
                onCreate() {
                    _this.updatedAt = Date.now();
                },
                onUpdate() {
                    _this.updatedAt = Date.now();
                    if (!window.Livewire || !_this.$wire) return;
                    if (!_this._tiptapInitialSyncDone) {
                        _this._tiptapInitialSyncDone = true;
                        return;
                    }
                    _this.$wire.set(config.wireProperty, editor.getHTML());
                },
                onSelectionUpdate() {
                    _this.updatedAt = Date.now();
                },
            });
        },
        isActive(type, opts = {}) {
            return editor ? editor.isActive(type, opts) : false;
        },
        toggleBold() {
            editor?.chain().focus().toggleBold().run();
        },
        toggleItalic() {
            editor?.chain().focus().toggleItalic().run();
        },
        toggleStrike() {
            editor?.chain().focus().toggleStrike().run();
        },
        toggleBulletList() {
            editor?.chain().focus().toggleBulletList().run();
        },
        toggleOrderedList() {
            editor?.chain().focus().toggleOrderedList().run();
        },
        toggleBlockquote() {
            editor?.chain().focus().toggleBlockquote().run();
        },
        setParagraph() {
            editor?.chain().focus().setParagraph().run();
        },
        toggleHeading(level = 1) {
            editor?.chain().focus().toggleHeading({ level }).run();
        },
        setCode() {
            editor?.chain().focus().toggleCode().run();
        },
        setLink() {
            const url = window.prompt('URL');
            if (url) editor?.chain().focus().setLink({ href: url }).run();
        },
    };
}
