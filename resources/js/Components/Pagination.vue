<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({ links: Array })

const label = (l) =>
  l === 'pagination.previous' ? '<i class="ri-arrow-left-s-line"></i>'
  : l === 'pagination.next' ? '<i class="ri-arrow-right-s-line"></i>'
  : l
</script>

<template>
  <div v-if="links.length > 3" class="join">
    <component
      :is="link.url ? Link : 'button'"
      v-for="(link, i) in links"
      :key="i"
      :href="link.url"
      :disabled="!link.url || null"
      :class="['join-item btn btn-sm', { 'btn-active': link.active, 'btn-disabled': !link.url }]"
      preserve-state
      preserve-scroll
      v-html="label(link.label)"
    />
  </div>
</template>