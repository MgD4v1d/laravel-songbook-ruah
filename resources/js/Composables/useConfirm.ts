import { reactive } from "vue";

interface ConfirmOptions {
    message: string;
    title?: string;
    confirmText?: string;
    danger?: boolean;
}

// Fuera de la función: un solo estado compartido por toda la app
const state = reactive({
    open: false,
    title: '',
    message: '',
    confirmText: '',
    danger: false,
    resolve: (_ok: boolean) => {},
});


const confirm = (options: ConfirmOptions | string): Promise<boolean> => {
    const o = typeof options === 'string' ? { message: options } : options;
    state.resolve(false); // si ya había una pregunta abierta, se responde "no"
    Object.assign(state, {
        open: true,
        title: o.title ?? '¿Estás seguro?',
        message: o.message,
        confirmText: o.confirmText ?? 'Aceptar',
        danger: o.danger ?? false,
    });
    return new Promise(resolve => { state.resolve = resolve; });
};

const answer = (ok: boolean) => {
    if (!state.open) return;
    state.open = false;
    state.resolve(ok);
};


export function useConfirm() {
    return { state, confirm, answer };
}