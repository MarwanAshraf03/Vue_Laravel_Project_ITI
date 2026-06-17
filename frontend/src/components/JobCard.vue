<script setup>
const props = defineProps(['job'])
const emit = defineEmits(['apply'])
</script>

<template>
  <article class="job-card">
    <div class="job-header">
      <h3 class="job-title">{{ job.title }}</h3>
      <span class="job-company">{{ job.company }}</span>
    </div>
    <div class="job-details">
      <span class="job-location">{{ job.location }}</span>
      <span class="job-salary">${{ job.salary_min }} - ${{ job.salary_max }}</span>
      <span class="job-experience">{{ job.experience_level }}</span>
    </div>
    <p class="job-description">{{ job.description.substring(0, 100) }}...</p>
    <button class="apply-btn" @click="$emit('apply', job)">Apply Now</button>
  </article>
</template>

<style scoped>
.job-card {
  background: white;
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(11, 28, 48, 0.05);
}

.job-header {
  margin-bottom: 0.75rem;
}

.job-title {
  margin: 0 0 0.25rem 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b1c30;
}

.job-company {
  color: #5f6b7c;
  font-size: 0.95rem;
}

.job-details {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.job-details span {
  background: #f3f7ff;
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  font-size: 0.85rem;
  color: #0b1c30;
}

.job-description {
  color: #5f6b7c;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.apply-btn {
  background: #0b1c30;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 999px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.apply-btn:hover {
  background: #1a3a5c;
}
</style>
