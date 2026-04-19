@props(['id' => 'markdown-editor', 'name' => 'content'])

@php
    $isLivewire = false;
    foreach ($attributes->getAttributes() as $key => $value) {
        if (str_starts_with($key, 'wire:model')) {
            $isLivewire = true;
            break;
        }
    }
@endphp

<div 
    x-data="{
        @if($isLivewire)
        content: @entangle($attributes->wire('model')),
        @else
        content: '',
        @endif
        editor: null,
        init() {
            this.editor = new EasyMDE({
                element: this.$refs.editor,
                initialValue: this.content,
                spellChecker: false,
                status: false,
                placeholder: 'Write your story in markdown...',
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                minHeight: '400px',
                maxHeight: '600px',
            });

            this.editor.codemirror.on('change', () => {
                this.content = this.editor.value();
                this.$refs.editor.value = this.content; // ensure textarea updates for normal form submissions
            });

            this.$watch('content', value => {
                if (value !== this.editor.value()) {
                    this.editor.value(value || '');
                }
            });
        }
    }"
    @if(!$isLivewire) x-modelable="content" @endif
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    class="w-full"
>
    <div class="prose max-w-none">
        <textarea x-ref="editor" id="{{ $id }}" name="{{ $name }}"></textarea>
    </div>

    <!-- Styles and Scripts for EasyMDE -->
    @once
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
            <style>
                .EasyMDEContainer .CodeMirror {
                    border-radius: 0 0 12px 12px;
                    border: 1px solid #e4e2e1;
                    background: #fbf9f8;
                    font-family: 'Inter', sans-serif;
                    font-size: 14px;
                }
                .EasyMDEContainer .editor-toolbar {
                    border-radius: 12px 12px 0 0;
                    border: 1px solid #e4e2e1;
                    background: #f0eded;
                    opacity: 1;
                }
                .EasyMDEContainer .editor-toolbar button {
                    border: none;
                }
                .EasyMDEContainer .editor-toolbar button.active, 
                .EasyMDEContainer .editor-toolbar button:hover {
                    background: #e4e2e1;
                }
            </style>
        @endpush
        @push('scripts')
            <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
        @endpush
    @endonce
</div>
