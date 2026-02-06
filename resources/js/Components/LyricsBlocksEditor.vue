<template>
  <div class="lyrics-blocks-editor">
    <!-- Toolbar para agregar bloques -->
    <div class="bg-base-200 p-4 rounded-lg mb-4 flex flex-wrap gap-2">
      <button 
        @click="addBlock('verse')" 
        class="btn btn-primary btn-sm gap-2"
        type="button"
      >
        <i class="ri-file-text-line"></i>
        Agregar Estrofa
      </button>
      <button 
        @click="addBlock('chorus')" 
        class="btn btn-warning btn-sm gap-2"
        type="button"
      >
        <i class="ri-music-2-line"></i>
        Agregar Coro
      </button>
      <button 
        @click="addBlock('bridge')" 
        class="btn btn-success btn-sm gap-2"
        type="button"
      >
        <i class="ri-link"></i>
        Agregar Puente
      </button>
    </div>

    <!-- Lista de bloques editables con drag & drop -->
    <draggable 
      v-model="localBlocks" 
      item-key="id"
      handle=".drag-handle"
      @end="emitUpdate"
      class="space-y-4"
    >
      <template #item="{ element: block, index }">
        <div 
          class="card shadow-lg transition-all hover:shadow-xl"
          :class="getCardClass(block.type)"
        >
          <div class="card-body p-4">
            <!-- Header del bloque -->
            <div class="flex items-center gap-2 mb-3 flex-wrap">
              <span class="drag-handle cursor-grab active:cursor-grabbing">
                <i class="ri-draggable text-xl opacity-50 hover:opacity-100"></i>
              </span>
              
              <div :class="getBadgeClass(block.type)">
                {{ getBlockLabel(block.type) }}
              </div>

              <input 
                v-model="block.label"
                type="text"
                class="input input-sm input-bordered flex-1 max-w-xs"
                placeholder="Etiqueta personalizada..."
                @input="emitUpdate"
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
              @input="emitUpdate"
              @keydown="handleKeydown($event, block.id)"
              class="textarea textarea-bordered w-full font-mono text-sm"
              :class="getTextareaClass(block.type)"
              :placeholder="getPlaceholder(block.type)"
              rows="4"
            ></textarea>

            <!-- Preview en vivo -->
            <div v-if="block.content" class="mt-3">
              <div class="text-xs opacity-70 mb-1 flex items-center gap-1">
                <i class="ri-eye-line"></i> Vista previa:
              </div>
              <div 
                class="p-3 rounded-lg text-sm leading-relaxed"
                :class="getPreviewClass(block.type)"
                v-html="renderMarkdown(block.content)"
              ></div>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <!-- Mensaje si no hay bloques -->
    <div v-if="localBlocks.length === 0" class="alert alert-info">
      <i class="ri-information-line text-xl"></i>
      <span>No hay bloques aún. Usa los botones de arriba para agregar estrofas, coros o puentes.</span>
    </div>

    <!-- Vista previa completa (colapsable) -->
    <div v-if="localBlocks.length > 0" class="mt-6">
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
            v-for="(block) in localBlocks" 
            :key="block.id"
            class="mb-4 p-4 rounded-lg"
            :class="getPreviewClass(block.type)"
          >
            <div class="font-bold text-sm mb-2 opacity-80">
              {{ block.label || getBlockLabel(block.type) }}
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

<script setup>
import { ref, watch, nextTick } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue']);

// Estado local
const localBlocks = ref([]);
const textareaRefs = ref({});
const showFullPreview = ref(false);

// Watch para cambios externos (immediate reemplaza onMounted)
watch(() => props.modelValue, (newValue) => {
  if (JSON.stringify(newValue) !== JSON.stringify(localBlocks.value)) {
    localBlocks.value = JSON.parse(JSON.stringify(newValue)).map(block => ({
      ...block,
      id: block.id || Date.now() + Math.random()
    }));
  }
}, { deep: true, immediate: true });

// Emitir cambios al padre
const emitUpdate = () => {
  emit('update:modelValue', JSON.parse(JSON.stringify(localBlocks.value)));
};

// Agregar nuevo bloque
const addBlock = (type) => {
  const newBlock = {
    id: Date.now(),
    type: type,
    content: '',
    label: generateDefaultLabel(type)
  };
  
  localBlocks.value.push(newBlock);
  emitUpdate();

  // Focus en el nuevo textarea
  nextTick(() => {
    const textarea = textareaRefs.value[newBlock.id];
    if (textarea) {
      textarea.focus();
    }
  });
};

// Eliminar bloque
const deleteBlock = (index) => {
  if (confirm('¿Estás seguro de eliminar este bloque?')) {
    localBlocks.value.splice(index, 1);
    emitUpdate();
  }
};

// Guardar referencias a textareas
const setTextareaRef = (el, blockId) => {
  if (el) {
    textareaRefs.value[blockId] = el;
  }
};

// Envolver/desenvolver selección con formato (toggle)
const wrapSelection = (blockId, wrapper) => {
  const textarea = textareaRefs.value[blockId];
  if (!textarea) return;

  const block = localBlocks.value.find(b => b.id === blockId);
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

  emitUpdate();
};

// Atajos de teclado
const handleKeydown = (event, blockId) => {
  if (event.ctrlKey && event.key === 'b') {
    event.preventDefault();
    wrapSelection(blockId, '**');
  }

  if (event.ctrlKey && event.key === 'i') {
    event.preventDefault();
    wrapSelection(blockId, '_');
  }
};

// Renderizar markdown
const renderMarkdown = (text) => {
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

// Helpers de estilo
const getCardClass = (type) => {
  const classes = {
    verse: 'border-l-4 border-primary bg-primary/5',
    chorus: 'border-l-4 border-warning bg-warning/5',
    bridge: 'border-l-4 border-success bg-success/5'
  };
  return classes[type] || '';
};

const getBadgeClass = (type) => {
  const classes = {
    verse: 'badge badge-primary',
    chorus: 'badge badge-warning',
    bridge: 'badge badge-success'
  };
  return classes[type] || 'badge';
};

const getTextareaClass = (type) => {
  const classes = {
    verse: 'textarea-primary',
    chorus: 'textarea-warning',
    bridge: 'textarea-success'
  };
  return classes[type] || '';
};

const getPreviewClass = (type) => {
  const classes = {
    verse: 'bg-primary/10 border-l-4 border-primary',
    chorus: 'bg-warning/10 border-l-4 border-warning',
    bridge: 'bg-success/10 border-l-4 border-success'
  };
  return classes[type] || '';
};

const getBlockLabel = (type) => {
  const labels = {
    verse: 'Estrofa',
    chorus: 'Coro',
    bridge: 'Puente'
  };
  return labels[type] || 'Bloque';
};

const generateDefaultLabel = (type) => {
  if (type === 'chorus') return 'Coro';
  if (type === 'bridge') return 'Puente';
  
  const verseCount = localBlocks.value.filter(b => b.type === 'verse').length;
  return `Estrofa ${verseCount + 1}`;
};

const getPlaceholder = (type) => {
  const placeholders = {
    verse: 'Escribe la estrofa aquí... Usa **negrita** y _cursiva_',
    chorus: 'Escribe el coro aquí... Usa **negrita** y _cursiva_',
    bridge: 'Escribe el puente aquí... Usa **negrita** y _cursiva_'
  };
  return placeholders[type] || 'Escribe aquí...';
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