<template>
  <div class="lyrics-blocks-editor">
    <!-- Toolbar para agregar bloques -->
    <div class="bg-base-200 p-4 rounded-lg mb-4 flex flex-wrap gap-2">
      <button
        v-for="(t, type) in BLOCK_TYPES"
        :key="type"
        type="button"
        @click="addBlock(type)"
        :class="['btn btn-sm gap-2', t.btn]"
      >
        <i :class="t.icon"></i> Agregar {{ t.label }}
      </button>
    </div>

    <!-- Lista de bloques editables con drag & drop -->
    <draggable 
      v-model="blocks" 
      item-key="id"
      handle=".drag-handle"
      class="space-y-4"
    >
      <template #item="{ element: block, index }: { element: LyricsBlock; index: number }">
        <div 
          class="card shadow-lg transition-all hover:shadow-xl"
          :class="['border-l-4', BLOCK_TYPES[block.type].card]"
        >
          <div class="card-body p-4">
            <!-- Header del bloque -->
            <div class="flex items-center gap-2 mb-3 flex-wrap">
              <span class="drag-handle cursor-grab active:cursor-grabbing">
                <i class="ri-draggable text-xl opacity-50 hover:opacity-100"></i>
              </span>
              
              <div :class="['badge', BLOCK_TYPES[block.type].badge]">
                {{ BLOCK_TYPES[block.type].label }}
              </div>

              <input 
                v-model="block.label"
                type="text"
                class="input input-sm input-bordered flex-1 max-w-xs"
                placeholder="Etiqueta personalizada..."
              />

              <!-- Botones de formato -->
              <div class="flex gap-1 ml-auto">
                <button
                  @mousedown.prevent="wrapSelection(block.id, '**')"
                  class="btn btn-sm btn-ghost tooltip"
                  type="button"
                  data-tip="Negrita (Ctrl+B)"
                >
                  <i class="ri-bold"></i>
                </button>
                <button
                  @mousedown.prevent="wrapSelection(block.id, '_')"
                  class="btn btn-sm btn-ghost tooltip"
                  type="button"
                  data-tip="Cursiva (Ctrl+I)"
                >
                  <i class="ri-italic"></i>
                </button>
                <button 
                  @click="deleteBlock(index)"
                  class="btn btn-sm btn-ghost text-error tooltip"
                  type="button"
                  data-tip="Eliminar"
                >
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>
            </div>

            <!-- Editor de texto -->
            <textarea
              v-model="block.content"
              :ref="el => setTextareaRef(el, block.id)"
              @keydown="handleKeydown($event, block.id)"
              class="textarea textarea-bordered w-full font-mono text-sm"
              :class="BLOCK_TYPES[block.type].textarea"
              placeholder="Escribe aquí... Usa **negrita** y _cursiva_"
              rows="4"
            ></textarea>

            <!-- Preview en vivo -->
            <div v-if="block.content" class="mt-3">
              <div class="text-xs opacity-70 mb-1 flex items-center gap-1">
                <i class="ri-eye-line"></i> Vista previa:
              </div>
              <div 
                class="p-3 rounded-lg text-sm leading-relaxed"
                :class="['border-l-4', BLOCK_TYPES[block.type].preview]"
                v-html="renderMarkdown(block.content)"
              ></div>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <!-- Mensaje si no hay bloques -->
    <div v-if="blocks.length === 0" class="alert alert-info">
      <i class="ri-information-line text-xl"></i>
      <span>No hay bloques aún. Usa los botones de arriba para agregar estrofas, coros o puentes.</span>
    </div>

    <!-- Vista previa completa (colapsable) -->
    <div v-if="blocks.length > 0" class="mt-6">
      <button 
        @click="showFullPreview = !showFullPreview"
        class="btn btn-outline btn-sm gap-2"
        type="button"
      >
        <i class="ri-eye-line" v-if="!showFullPreview"></i>
        <i class="ri-eye-off-line" v-else></i>
        {{ showFullPreview ? 'Ocultar' : 'Mostrar' }} vista previa completa
      </button>

      <div v-show="showFullPreview" class="card bg-base-100 shadow-xl mt-4">
        <div class="card-body">
          <h4 class="card-title text-lg">
            <i class="ri-music-2-fill"></i>
            Vista previa completa
          </h4>
          
          <div class="divider"></div>

          <div 
            v-for="(block) in blocks" 
            :key="block.id"
            class="mb-4 p-4 rounded-lg"
            :class="['border-l-4', BLOCK_TYPES[block.type].preview]"
          >
            <div class="font-bold text-sm mb-2 opacity-80">
              {{ block.label || BLOCK_TYPES[block.type].label }}
            </div>
            <div 
              class="leading-relaxed"
              v-html="renderMarkdown(block.content)"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import draggable from 'vuedraggable';
import type { BlockType, LyricsBlock } from '@/types/song';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

interface BlockStyle {
  label: string;
  icon: string;
  btn: string;
  badge: string;
  textarea: string;
  card: string;
  preview: string;
}


// Clases completas (no armadas con `${color}`) para que Tailwind las detecte
const BLOCK_TYPES: Record<BlockType, BlockStyle> = {
  verse:  { label: 'Estrofa', icon: 'ri-file-text-line', btn: 'btn-primary', badge: 'badge-primary', textarea: 'textarea-primary', card: 'border-primary bg-primary/5', preview: 'border-primary bg-primary/10' },
  chorus: { label: 'Coro',    icon: 'ri-music-2-line',   btn: 'btn-warning', badge: 'badge-warning', textarea: 'textarea-warning', card: 'border-warning bg-warning/5', preview: 'border-warning bg-warning/10' },
  bridge: { label: 'Puente',  icon: 'ri-link',           btn: 'btn-success', badge: 'badge-success', textarea: 'textarea-success', card: 'border-success bg-success/5', preview: 'border-success bg-success/10' },
};

const blocks = defineModel<LyricsBlock[]>({ default: () => [] });

const textareaRefs: Record<number, HTMLTextAreaElement> = {};

const showFullPreview = ref(false);
const newId = () => Date.now() + Math.random();

// Letras guardadas sin id: draggable necesita uno por bloque
blocks.value.forEach(b => { b.id ??= newId(); });


// Agregar nuevo bloque
const addBlock = (type: BlockType) => {
  const verses = blocks.value.filter(b => b.type === 'verse').length;
  const block: LyricsBlock = {
    id: newId(),
    type,
    content: '',
    label: type === 'verse' ? `Estrofa ${verses + 1}` : BLOCK_TYPES[type].label,
  };
  blocks.value = [...blocks.value, block];
  nextTick(() => textareaRefs[block.id]?.focus());
};

// Eliminar bloque
const deleteBlock = async (index: number) => {
  if (await confirm({ message: 'Se eliminará este bloque de la letra.', confirmText: 'Eliminar', danger: true })) {
    blocks.value = blocks.value.filter((_, i) => i !== index);
  }
};

// Guardar referencias a textareas
const setTextareaRef = (el: unknown, blockId: number) => {
  if (el instanceof HTMLTextAreaElement) textareaRefs[blockId] = el;
};

// Envolver/desenvolver selección con formato (toggle)
const wrapSelection = (blockId: number, wrapper: '**' | '_') => {
  const textarea = textareaRefs[blockId];
  if (!textarea) return;

  const block = blocks.value.find(b => b.id === blockId);
  if (!block) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = block.content;
  const wLen = wrapper.length;

  if (start !== end) {
    const selected = text.substring(start, end);
    const lines = selected.split('\n');

    // Verificar si TODAS las líneas ya están envueltas
    const allWrapped = lines.every(line =>
      line.trim() === '' || (line.startsWith(wrapper) && line.endsWith(wrapper) && line.length >= wLen * 2)
    );

    if (allWrapped) {
      // Toggle off: quitar wrapper de cada línea
      const unwrapped = lines.map(line => {
        if (line.trim() === '') return line;
        return line.substring(wLen, line.length - wLen);
      }).join('\n');
      block.content = text.substring(0, start) + unwrapped + text.substring(end);

      nextTick(() => {
        textarea.focus();
        textarea.setSelectionRange(start, start + unwrapped.length);
      });
    } else {
      // Toggle on: agregar wrapper a cada línea
      const wrapped = lines.map(line => {
        if (line.trim() === '') return line;
        return wrapper + line + wrapper;
      }).join('\n');
      block.content = text.substring(0, start) + wrapped + text.substring(end);

      nextTick(() => {
        textarea.focus();
        textarea.setSelectionRange(start, start + wrapped.length);
      });
    }
  } else {
    // Sin selección: insertar marcadores vacíos
    block.content =
      text.substring(0, start) +
      wrapper + wrapper +
      text.substring(start);

    nextTick(() => {
      textarea.focus();
      textarea.setSelectionRange(start + wLen, start + wLen);
    });
  }
};

// Atajos de teclado
const handleKeydown = (event: KeyboardEvent, blockId: number) => {
  if (!(event.ctrlKey || event.metaKey)) return;
  if (event.key === 'b') { event.preventDefault(); wrapSelection(blockId, '**'); }
  if (event.key === 'i') { event.preventDefault(); wrapSelection(blockId, '_'); }
};

// Renderizar markdown
const renderMarkdown = (text: string) => {
  if (!text) return '';

  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/\*\*([\s\S]*?)\*\*/g, '<strong>$1</strong>')
    .replace(/_([\s\S]*?)_/g, '<em>$1</em>')
    .replace(/\n/g, '<br>');
};
</script>

<style scoped>
.lyrics-blocks-editor {
  width: 100%;
}

.drag-handle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.25rem;
  transition: all 0.2s;
}

.drag-handle:hover {
  transform: scale(1.1);
}

.textarea {
  min-height: 100px;
  resize: vertical;
}

:deep(strong) {
  font-weight: 700;
}

:deep(em) {
  font-style: italic;
}
</style>