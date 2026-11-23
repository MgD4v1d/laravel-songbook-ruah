<script setup lang="ts">
import { ref, watch } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { state, answer } = useConfirm();
const dialog = ref<HTMLDialogElement>();

watch(() => state.open, (open) => {
    if (open) dialog.value?.showModal();
    else dialog.value?.close();
});
</script>

<template>
    <!-- @close cubre Esc y clic afuera: cuentan como "Cancelar" -->
    <dialog ref="dialog" class="modal" @close="answer(false)">
        <div class="modal-box">
            <h3 class="text-lg font-bold">{{ state.title }}</h3>
            <p class="py-4">{{ state.message }}</p>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" @click="answer(false)">Cancelar</button>
                <button
                    type="button"
                    class="btn"
                    :class="state.danger ? 'btn-error' : 'btn-primary'"
                    @click="answer(true)"
                >
                    {{ state.confirmText }}
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>Cerrar</button>
        </form>
    </dialog>
</template>