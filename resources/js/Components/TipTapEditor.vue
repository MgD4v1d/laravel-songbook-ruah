<template>
  <div class="editor-container border rounded-lg overflow-hidden bg-base-100 border-base-300">
    <div v-if="editor" class="toolbar flex flex-wrap gap-1 p-2 bg-base-200 border-b border-base-300">
        
      <!-- Headings -->
      <button 
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" 
        :class="{ 'btn-active': editor.isActive('heading', { level: 2 }) }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Heading 2"
      >
        <i class="ri-h-2"></i>
      </button>

      <div class="divider divider-horizontal mx-0"></div>

      <!-- Basic Formatting -->
      <button 
        @click="editor.chain().focus().toggleBold().run()" 
        :class="{ 'btn-active': editor.isActive('bold') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Bold"
      >
        <i class="ri-bold"></i>
      </button>

      <button 
        @click="editor.chain().focus().toggleItalic().run()" 
        :class="{ 'btn-active': editor.isActive('italic') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Italic"
      >
        <i class="ri-italic"></i>
      </button>

       <button 
        @click="editor.chain().focus().toggleStrike().run()" 
        :class="{ 'btn-active': editor.isActive('strike') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Strike"
      >
        <i class="ri-strikethrough"></i>
      </button>

      <div class="divider divider-horizontal mx-0"></div>

      <!-- Lists -->
      <button 
        @click="editor.chain().focus().toggleBulletList().run()" 
        :class="{ 'btn-active': editor.isActive('bulletList') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Bullet List"
      >
        <i class="ri-list-unordered"></i>
      </button>

      <button 
        @click="editor.chain().focus().toggleOrderedList().run()" 
        :class="{ 'btn-active': editor.isActive('orderedList') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Ordered List"
      >
        <i class="ri-list-ordered"></i>
      </button>

      <div class="divider divider-horizontal mx-0"></div>

       <!-- Blockquote & Code -->
      <button 
        @click="editor.chain().focus().toggleBlockquote().run()" 
        :class="{ 'btn-active': editor.isActive('blockquote') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Quote"
      >
        <i class="ri-double-quotes-l"></i>
      </button>

      <button 
        @click="editor.chain().focus().toggleCodeBlock().run()" 
        :class="{ 'btn-active': editor.isActive('codeBlock') }"
        class="btn btn-xs btn-ghost"
        type="button"
        title="Code Block"
      >
        <i class="ri-code-box-line"></i>
      </button>
      
      <div class="divider divider-horizontal mx-0"></div>

      <!-- Horizontal Rule -->
      <button 
        @click="editor.chain().focus().setHorizontalRule().run()" 
        class="btn btn-xs btn-ghost"
        type="button"
        title="Horizontal Rule"
      >
        <i class="ri-separator"></i>
      </button>

       <!-- Undo/Redo -->
       <div class="ml-auto flex gap-1">
            <button 
                @click="editor.chain().focus().undo().run()" 
                :disabled="!editor.can().chain().focus().undo().run()"
                class="btn btn-xs btn-ghost"
                type="button"
                title="Undo"
            >
                <i class="ri-arrow-go-back-line"></i>
            </button>
            <button 
                @click="editor.chain().focus().redo().run()" 
                :disabled="!editor.can().chain().focus().redo().run()"
                class="btn btn-xs btn-ghost"
                type="button"
                title="Redo"
            >
                <i class="ri-arrow-go-forward-line"></i>
            </button>
       </div>

    </div>

    <!-- Editor Content -->
    <editor-content :editor="editor" class="editor-content p-4 min-h-[300px] prose max-w-none focus:outline-none" :style="{ minHeight: height }" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'
import Underline from '@tiptap/extension-underline'
import { marked } from 'marked'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
      type: String,
      default: 'Escribe aquí...'
  },
  height: {
      type: String,
      default: '500px'
  }
})

const emit = defineEmits(['update:modelValue'])

/* 
 * Helper to detect if content is likely Markdown (has standard markdown symbols) 
 * but NOT HTML (doesn't start with tags). 
 * This is a simple heuristic for migration.
 */
const isMarkdown = (text) => {
    if (!text) return false;
    // If it looks like HTML, assume it is HTML
    if (text.trim().startsWith('<') || text.includes('</p>')) return false;
    
    // Check for common markdown patterns
    const markdownPatterns = [
        /^#+\s/m,        // Headers
        /^[-*]\s/m,      // Lists
        /\*\*.+\*\*/,    // Bold
        /\[.+\]\(.+\)/,  // Links
    ];
    
    return markdownPatterns.some(pattern => pattern.test(text));
}

// Prepare initial content
let initialContent = props.modelValue;

// If legacy content seems to be markdown, convert it once on load
if (isMarkdown(initialContent)) {
    initialContent = marked.parse(initialContent);
}

const editor = useEditor({
  content: initialContent,
  extensions: [
    StarterKit,
    Underline,
    Link.configure({
      openOnClick: false,
    }),
    Placeholder.configure({
        placeholder: props.placeholder,
    })
  ],
  editorProps: {
      attributes: {
          class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[150px]',
      },
  },
  onUpdate: ({ editor }) => {
    // Emit HTML content
    emit('update:modelValue', editor.getHTML())
  },
})

// Watch for external changes
watch(() => props.modelValue, (value) => {
  // Only update if the content is different to avoid cursor jumping
  // And also handle the case where we might receive raw Markdown again if something weird happens (unlikely in normal flow)
  const isNewContent = editor.value.getHTML() !== value
  if (isNewContent) {
      // If the new value looks like markdown (e.g. from a reset), sanitize it
      let contentToSet = value;
      if (isMarkdown(value)) {
          contentToSet = marked.parse(value);
      }
      editor.value.commands.setContent(contentToSet, false)
  }
})

onBeforeUnmount(() => {
  editor.value.destroy()
})
</script>

<style>
/* Custom styles for the editor content area */
.editor-content .ProseMirror {
    outline: none;
    min-height: 100%;
}

.editor-content .ProseMirror p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
</style>
