<script setup>
import { RouterView } from 'vue-router'
import Footer from '@/components/Footer.vue'
</script>

<template>
  <div class="app-layout">
    <main class="main-content">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<style>
/* Global reset - no overflow */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body, #app {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

body {
  font-family: 'Inter', sans-serif;
  background: #f8f9ff;
  color: #0b1c30;
}

/* Remove default scrollbar only when not needed */
#app {
  display: flex;
  flex-direction: column;
}
</style>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
}

.main-content {
  flex: 1 0 auto;
  display: flex;
  flex-direction: column;
}
</style>