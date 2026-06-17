<script setup>
import { ref } from 'vue'
import { applyToJob } from '@/services/jobsService'
import { getCurrentUser } from '@/services/userService'

const props = defineProps(['job', 'isOpen'])
const emit = defineEmits(['close'])

const applyMode = ref('contact') // 'contact' or 'resume'
const applicationForm = ref({
  candidate_name: '',
  email: '',
  phone: '',
  message: '',
  resume: null,
})
const loading = ref(false)
const error = ref(null)
const success = ref(null)

async function initForm() {
  const user = await getCurrentUser()
  if (user) {
    applicationForm.value.candidate_name = user.name
    applicationForm.value.email = user.email
  }
}

async function handleApply() {
  loading.value = true
  error.value = null
  success.value = null

  const result = await applyToJob(props.job.id, applicationForm.value)
  if (result.ok) {
    success.value = 'Application submitted successfully!'
    setTimeout(() => {
      emit('close')
    }, 2000)
  } else {
    error.value = result.message || 'Failed to submit application'
  }
  loading.value = false
}

function handleFileChange(e) {
  applicationForm.value.resume = e.target.files[0]
}
</script>

<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-content">
        <button class="close-btn" @click="$emit('close')">×</button>
        <h2>Apply to {{ job.title }}</h2>

        <div class="mode-tabs">
          <button :class="{ active: applyMode === 'contact' }" @click="applyMode = 'contact'">
            Contact Info
          </button>
          <button :class="{ active: applyMode === 'resume' }" @click="applyMode = 'resume'">
            Upload Resume
          </button>
        </div>

        <form @submit.prevent="handleApply" class="apply-form">
          <div v-if="applyMode === 'contact'">
            <div class="form-group">
              <label>Name</label>
              <input type="text" v-model="applicationForm.candidate_name" required />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" v-model="applicationForm.email" required />
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" v-model="applicationForm.phone" />
            </div>
          </div>

          <div v-if="applyMode === 'resume'">
            <div class="form-group">
              <label>Resume (PDF, DOC, DOCX)</label>
              <input type="file" accept=".pdf,.doc,.docx" @change="handleFileChange" required />
            </div>
            <div class="form-group">
              <label>Message (Optional)</label>
              <textarea v-model="applicationForm.message" rows="4"></textarea>
            </div>
          </div>

          <div v-if="error" class="error-msg">{{ error }}</div>
          <div v-if="success" class="success-msg">{{ success }}</div>

          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Submitting...' : 'Submit Application' }}
          </button>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  width: 100%;
  max-width: 500px;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: #5f6b7c;
}

.modal-content h2 {
  margin-top: 0;
  color: #0b1c30;
}

.mode-tabs {
  display: flex;
  gap: 0.5rem;
  margin: 1rem 0;
}

.mode-tabs button {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid rgba(11, 28, 48, 0.2);
  background: white;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
}

.mode-tabs button.active {
  background: #0b1c30;
  color: white;
  border-color: #0b1c30;
}

.apply-form {
  margin-top: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.35rem;
  color: #5f6b7c;
  font-weight: 600;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid rgba(11, 28, 48, 0.2);
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
}

.submit-btn {
  width: 100%;
  padding: 1rem;
  background: #0b1c30;
  color: white;
  border: none;
  border-radius: 999px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 0.5rem;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-msg {
  color: #dc2626;
  margin: 0.75rem 0;
  font-weight: 600;
}

.success-msg {
  color: #059669;
  margin: 0.75rem 0;
  font-weight: 600;
}
</style>
