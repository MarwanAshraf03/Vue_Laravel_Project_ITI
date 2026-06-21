<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  icon: { type: String, default: 'fa-chart-simple' },
  accent: { type: String, default: '#006a61' },
})
</script>

<template>
  <div class="bento-card">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <p class="small fw-semibold text-muted text-uppercase tracking-wider mb-0">{{ label }}</p>
      <i class="fa-solid fs-5" :class="icon" :style="{ color: accent }"></i>
    </div>
    <p class="display-6 fw-bold mb-0">{{ value }}</p>
  </div>
</template>

<style scoped>
.bento-card {
  background-color: #ffffff;
  border: 1px solid #c6c6cd;
  border-radius: 12px;
  padding: 24px;
  height: 100%;
}
</style>
