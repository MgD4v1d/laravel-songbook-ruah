<template>
    <div class="markdown-editor-wrapper">
        <div ref="editorRef"></div>
    </div>
</template>

<script setup>

import { ref, onMounted, watch } from 'vue';
import Editor from '@toast-ui/editor';
import '@toast-ui/editor/dist/toastui-editor.css';


const props = defineProps({
    modelValue:{
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Escribe la letra de la canción...'
    },
    height: {
        type: String,
        default: '500px'
    }
});

const emit = defineEmits(['update:modelValue']);

const editorRef = ref(null);
let editorInstance = null;

onMounted(() => {
    editorInstance = new Editor({
        el: editorRef.value,
        height:props.height,
        initialEditType: 'wysiwyg',
        previewStyle: 'vertical',
        placeholder:props.placeholder,
        initialValue: props.modelValue,
        hideModeSwitch: false,
        usageStatistics: false,
        linkAttributes: {
            target: '_blank'
        },
        extendedAutolinks: true,
        customHTMLRenderer:{
            text(node){
                return {
                    type: 'html',
                    content: node.literal
                }
            } 
        },
        toolbarItems: [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task'],
            ['table', 'link'],
            ['code', 'codeblock']
        ],
        events: {
            change: () => {
                const markdown = editorInstance.getMarkdown();
                emit('update:modelValue', markdown);
            }
        }
    });
});

watch(() => props.modelValue, (newValue) =>{
    if(editorInstance && editorInstance.getMarkdown() !== newValue){
        editorInstance.setMarkdown(newValue || '');
    }
});

</script>

<style scoped>
.markdown-editor-wrapper{
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    overflow: hidden;
}

.markdown-editor-wrapper .toastui-editor-defaultUI {
    border: none;
}

.markdown-editor-wrapper .toastui-editor-toolbar {
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.markdown-editor-wrapper .toastui-editor-main {
    background-color: white;
}

/* Mejorar estilo de preview */
.markdown-editor-wrapper .toastui-editor-contents h1,
.markdown-editor-wrapper .toastui-editor-contents h2 {
    color: #1f2937;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.markdown-editor-wrapper .toastui-editor-contents h2 {
    font-size: 1.5rem;
    color: #2563eb;
}

.markdown-editor-wrapper .toastui-editor-contents p {
    line-height: 1.8;
    margin-bottom: 1rem;
}

/* Estilo para acordes (texto en negrita) */
.markdown-editor-wrapper .toastui-editor-contents strong {
    color: #dc2626;
    font-weight: 700;
    font-size: 0.9em;
}
</style>