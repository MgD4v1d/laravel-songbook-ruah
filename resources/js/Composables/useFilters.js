import { reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

export function useFilters(url, initial, defaults) {
    // Solo toma las llaves de defaults; si el servidor manda null, usa el valor por defecto
    const filters = reactive(
        Object.fromEntries(Object.entries(defaults).map(([k, v]) => [k, initial[k] ?? v]))
    )
    let timer

    const apply = () => {
        clearTimeout(timer)
        router.get(url, filters, { preserveState: true, replace: true })
    }

    // Un solo watch para todos los filtros; solo el texto espera a que dejes de escribir
    watch(() => ({ ...filters }), (now, before) => {
        clearTimeout(timer)
        timer = setTimeout(apply, now.search !== before.search ? 500 : 0)
    })

    const reset = () => Object.assign(filters, defaults)

    const isActive = computed(() =>
        Object.keys(defaults).some(k => filters[k] !== defaults[k])
    )

    return { filters, apply, reset, isActive }
}