<script setup></script>

<template>
  <footer class="border-top bg-light py-4">
    <div
      class="d-flex flex-column flex-md-row justify-content-between align-items-center container gap-3"
    >
      <div class="fw-bold">Jobify</div>

      <div class="d-flex justify-content-center flex-wrap gap-3">
        <a href="#" class="text-decoration-none text-secondary">Privacy Policy</a>
        <a href="#" class="text-decoration-none text-secondary">Terms</a>
        <a href="#" class="text-decoration-none text-secondary">Support</a>
      </div>

      <small class="text-secondary">© {{ new Date().getFullYear() }} Jobify</small>
    </div>
  </footer>
</template>
<style scoped></style>
