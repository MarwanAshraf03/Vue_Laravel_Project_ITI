<script setup>
import { RouterLink } from 'vue-router'

const props = defineProps(['job'])
const emit = defineEmits(['apply'])

function techList(techs) {
  if (!techs) return []
  return techs.split(',').map((t) => t.trim()).filter(Boolean).slice(0, 4)
}

function formatWorkType(wt) {
  if (!wt) return ''
  return wt.charAt(0).toUpperCase() + wt.slice(1)
}
</script>

<template>
  <article class="job-card">
    <div class="job-header">
      <div class="job-header-text">
        <h3 class="job-title">{{ job.title }}</h3>
        <span class="job-company">{{ job.company }}</span>
      </div>
      <span v-if="job.work_type" class="work-type-badge">{{ formatWorkType(job.work_type) }}</span>
    </div>

    <div class="job-details">
      <span v-if="job.location" class="job-location">📍 {{ job.location }}</span>
      <span class="job-salary">💰 ${{ job.salary_min }} – ${{ job.salary_max }}</span>
      <span v-if="job.experience_level" class="job-experience">🎓 {{ job.experience_level }}</span>
      <span v-if="job.category" class="job-category">🏷️ {{ job.category }}</span>
    </div>

    <div v-if="techList(job.technologies).length" class="tech-tags">
      <span v-for="tech in techList(job.technologies)" :key="tech" class="tech-tag">{{ tech }}</span>
    </div>

    <p class="job-description">{{ job.description?.substring(0, 120) }}...</p>

    <div class="card-actions">
      <RouterLink :to="{ name: 'job_details', params: { id: job.id } }" class="view-btn">
        View Details
      </RouterLink>
      <button class="apply-btn" @click="$emit('apply', job)">Apply Now</button>
    </div>
  </article>
</template>

<style scoped>
.job-card {
  background: white;
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(11, 28, 48, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.job-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 28px rgba(11, 28, 48, 0.1);
}

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.5rem;
}

.job-header-text {
  flex: 1;
}

.job-title {
  margin: 0 0 0.25rem 0;
  font-size: 1.15rem;
  font-weight: 700;
  color: #0b1c30;
}

.job-company {
  color: #5f6b7c;
  font-size: 0.92rem;
}

.work-type-badge {
  background: #e7f4f3;
  color: #006a61;
  padding: 0.25rem 0.7rem;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid rgba(0, 106, 97, 0.2);
  white-space: nowrap;
}

.job-details {
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.job-details span {
  background: #f3f7ff;
  padding: 0.3rem 0.7rem;
  border-radius: 999px;
  font-size: 0.8rem;
  color: #0b1c30;
}

.tech-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.tech-tag {
  background: #f0fdf4;
  color: #15803d;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  border: 1px solid rgba(21, 128, 61, 0.18);
}

.job-description {
  color: #5f6b7c;
  line-height: 1.5;
  font-size: 0.9rem;
  margin: 0;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 0.25rem;
}

.view-btn {
  flex: 1;
  background: #f3f7ff;
  color: #0b1c30;
  border: 1px solid rgba(11, 28, 48, 0.15);
  padding: 0.65rem 1rem;
  border-radius: 999px;
  font-weight: 700;
  text-align: center;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background 0.2s;
}

.view-btn:hover {
  background: #e7eeff;
}

.apply-btn {
  flex: 1;
  background: #0b1c30;
  color: white;
  border: none;
  padding: 0.65rem 1rem;
  border-radius: 999px;
  font-weight: 700;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background 0.2s;
}

.apply-btn:hover {
  background: #1a3a5c;
}
</style>
