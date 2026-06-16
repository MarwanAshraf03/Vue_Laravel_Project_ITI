<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import { deleteCandidateProfile, getCurrentUser, updateCandidateProfile } from '@/services/userService'

const router = useRouter()
const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const success = ref('')
const error = ref('')
const profile = ref(null)
const selectedFile = ref(null)
const dragActive = ref(false)
const imagePreview = ref('')
const fileInput = ref(null)

const form = reactive({
  name: '',
  email: '',
  password: '',
  job_title: '',
})

const initials = computed(() => {
  if (!profile.value?.name) return 'C'
  return profile.value.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join('')
    .toUpperCase()
})

function syncForm(data) {
  profile.value = data
  form.name = data?.name || ''
  form.email = data?.email || ''
  form.password = ''
  form.job_title = data?.job_title || ''
  selectedFile.value = null
  imagePreview.value = resolveImageUrl(data?.profile_picture || '')
}

function resolveImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
    return path
  }
  return `/storage/${path}`
}

function openFilePicker() {
  fileInput.value?.click()
}

function handleFile(file) {
  if (!file) return
  if (!file.type.startsWith('image/')) {
    error.value = 'Please choose an image file.'
    return
  }

  selectedFile.value = file
  imagePreview.value = URL.createObjectURL(file)
  error.value = ''
}

function onFileChange(event) {
  handleFile(event.target.files?.[0])
}

function onDrop(event) {
  event.preventDefault()
  dragActive.value = false
  handleFile(event.dataTransfer.files?.[0])
}

async function loadProfile() {
  loading.value = true
  error.value = ''
  const data = await getCurrentUser()

  if (!data) {
    router.push({ name: 'user_login' })
    return
  }

  if (data.role !== 'candidate') {
    router.push({ name: 'home' })
    return
  }

  syncForm(data)
  loading.value = false
}

function formatErrors(responseErrors) {
  if (!responseErrors) return ''
  return Object.values(responseErrors)
    .flat()
    .join(' • ')
}

async function saveProfile() {
  saving.value = true
  error.value = ''
  success.value = ''

  const result = await updateCandidateProfile({
    ...form,
    profile_picture_file: selectedFile.value,
  })

  saving.value = false

  if (!result.ok) {
    error.value = formatErrors(result.errors) || result.message
    return
  }

  syncForm(result.data.user)
  success.value = 'Profile updated successfully.'
}

async function removeProfile() {
  const confirmed = window.confirm(
    'Delete your candidate account? This will permanently remove your profile.',
  )

  if (!confirmed) return

  deleting.value = true
  error.value = ''

  const result = await deleteCandidateProfile()

  deleting.value = false

  if (!result.ok) {
    error.value = result.message
    return
  }

  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

onMounted(loadProfile)
</script>

<template>
  <main class="profile-page">
    <section class="profile-hero">
      <div class="profile-copy">
        <p class="eyebrow">Candidate profile</p>
        <h1>Manage your public profile and account details.</h1>
      </div>

      <div class="profile-badge" v-if="profile">
        <img v-if="imagePreview" class="badge-image" :src="imagePreview" alt="Profile picture" />
        <span v-else>{{ initials }}</span>
        <div>
          <strong>{{ profile.name }}</strong>
          <small>{{ profile.email }}</small>
        </div>
      </div>
    </section>

    <section class="profile-grid" v-if="!loading">
      <div class="profile-card">
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-if="success" class="alert alert-success">{{ success }}</div>

        <form @submit.prevent="saveProfile" class="w-full">
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input v-model="form.name" type="text" class="form-control form-control-lg" />
          </div>

          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input v-model="form.email" type="email" class="form-control form-control-lg" />
          </div>

          <div class="mb-3">
            <label class="form-label">Professional headline</label>
            <input v-model="form.job_title" type="text" class="form-control form-control-lg" />
          </div>

          <div class="mb-4">
            <label class="form-label">Profile picture</label>
            <!-- Circular avatar upload area -->
            <div
              class="avatar-upload"
              @click="openFilePicker"
              @dragover.prevent="dragActive = true"
              @dragleave.prevent="dragActive = false"
              @drop.prevent="onDrop"
              :class="{ 'drag-active': dragActive }"
            >
              <input ref="fileInput" type="file" accept="image/*" class="d-none" @change="onFileChange" />
              <div class="avatar-preview">
                <img v-if="imagePreview" :src="imagePreview" alt="Profile picture" />
                <div v-else class="avatar-placeholder">{{ initials }}</div>
                <div class="avatar-overlay">
                  <span class="material-symbols-outlined">camera_alt</span>
                </div>
              </div>
              <p class="avatar-hint">Click or drag an image to change your avatar</p>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">New password</label>
            <input
              v-model="form.password"
              type="password"
              class="form-control form-control-lg"
              placeholder="Leave blank to keep the current password"
            />
          </div>

          <!-- Puffed up buttons -->
          <div class="action-buttons">
            <button type="submit" class="btn btn-primary btn-lg" :disabled="saving">
              <span v-if="!saving">Save changes</span>
              <span v-else>Saving...</span>
            </button>

            <button type="button" class="btn btn-outline-secondary btn-lg" @click="logout">
              Logout
            </button>

            <button type="button" class="btn btn-outline-danger btn-lg" @click="removeProfile" :disabled="deleting">
              <span v-if="!deleting">Delete account</span>
              <span v-else>Deleting...</span>
            </button>
          </div>
        </form>
      </div>

      <aside class="profile-card profile-aside">
        <h2>Profile preview</h2>
        <div class="preview-item">
          <span>Name</span>
          <strong>{{ form.name || '—' }}</strong>
        </div>
        <div class="preview-item">
          <span>Email</span>
          <strong>{{ form.email || '—' }}</strong>
        </div>
        <div class="preview-item">
          <span>Headline</span>
          <strong>{{ form.job_title || 'Add a headline to describe your role.' }}</strong>
        </div>
        <div class="preview-item">
          <span>Profile picture</span>
          <div class="preview-avatar">
            <img v-if="imagePreview" :src="imagePreview" alt="Preview avatar" />
            <div v-else class="preview-placeholder">{{ initials }}</div>
            <span class="preview-filename">{{ selectedFile?.name || 'Current image' }}</span>
          </div>
        </div>
      </aside>
    </section>

    <section v-else class="loading-state">
      Loading your profile...
    </section>
  </main>
</template>

<style scoped>
.profile-page {
  min-height: 100vh;
  padding: 2rem 1.25rem 3rem;
  background:
    radial-gradient(circle at top left, rgba(0, 106, 97, 0.12), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.16), transparent 30%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
}

.profile-hero,
.profile-grid {
  max-width: 1120px;
  margin: 0 auto;
}

.profile-hero {
  display: flex;
  justify-content: space-between;
  gap: 1.5rem;
  align-items: center;
  margin-bottom: 1.5rem;
}

.profile-copy h1 {
  margin: 0 0 0.75rem;
  font-size: clamp(2rem, 4vw, 3.5rem);
  line-height: 1.05;
}

.profile-copy p {
  max-width: 60ch;
  color: #5f6b7c;
  margin: 0;
}

.eyebrow {
  display: inline-block;
  margin-bottom: 0.75rem;
  color: #006a61;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.75rem;
  font-weight: 700;
}

.profile-badge {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 22px;
  padding: 1rem 1.1rem;
  box-shadow: 0 18px 50px rgba(11, 28, 48, 0.08);
}

.profile-badge span {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #006a61, #0b1c30);
  color: #fff;
  font-weight: 800;
}

.badge-image {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.75);
}

.profile-badge strong,
.profile-badge small {
  display: block;
}

.profile-badge small {
  color: #5f6b7c;
}

.profile-grid {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 1.25rem;
}

.profile-card {
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 28px;
  padding: 1.5rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
}

/* ===== NEW: Avatar upload area (circular) ===== */
.avatar-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
}

.avatar-preview {
  position: relative;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  background: #f0f2f5;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.avatar-preview:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0, 106, 97, 0.2);
}

.avatar-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #006a61, #0b1c30);
  color: white;
  font-size: 3rem;
  font-weight: 700;
}

.avatar-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
  transition: opacity 0.2s;
  opacity: 0;
}

.avatar-preview:hover .avatar-overlay {
  opacity: 1;
}

.avatar-hint {
  font-size: 0.8rem;
  color: #5f6b7c;
  margin: 0;
}

.drag-active .avatar-preview {
  border: 3px solid #006a61;
  transform: scale(1.02);
}

/* ===== Puffed up action buttons ===== */
.action-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 2rem;
  justify-content: flex-start;
}

.btn-lg {
  padding: 0.75rem 1.8rem;
  font-size: 1rem;
  border-radius: 40px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-primary {
  background: #006a61;
  border-color: #006a61;
}
.btn-primary:hover:not(:disabled) {
  background: #005049;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 106, 97, 0.25);
}

.btn-outline-secondary {
  border-color: #cbd5e1;
  color: #334155;
}
.btn-outline-secondary:hover {
  background: #f8fafc;
  transform: translateY(-1px);
}

.btn-outline-danger {
  border-color: #fecaca;
  color: #b91c1c;
}
.btn-outline-danger:hover:not(:disabled) {
  background: #fef2f2;
  transform: translateY(-1px);
}

/* ===== Preview sidebar improvements ===== */
.preview-item {
  padding: 1rem 0;
  border-bottom: 1px solid rgba(11, 28, 48, 0.08);
}
.preview-item:last-child {
  border-bottom: none;
}
.preview-item span {
  display: block;
  color: #5f6b7c;
  font-size: 0.85rem;
  margin-bottom: 0.35rem;
}
.preview-item strong {
  word-break: break-word;
}
.preview-avatar {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.5rem;
}
.preview-avatar img,
.preview-placeholder {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  background: linear-gradient(135deg, #006a61, #0b1c30);
  color: white;
  display: grid;
  place-items: center;
  font-weight: bold;
  font-size: 1.2rem;
}
.preview-filename {
  font-size: 0.85rem;
  color: #475569;
  word-break: break-word;
}

.loading-state {
  max-width: 1120px;
  margin: 3rem auto 0;
  padding: 2rem;
  text-align: center;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.82);
  border: 1px solid rgba(11, 28, 48, 0.08);
}

@media (max-width: 900px) {
  .profile-hero,
  .profile-grid {
    grid-template-columns: 1fr;
    flex-direction: column;
  }
  .profile-badge {
    width: 100%;
  }
  .action-buttons {
    justify-content: center;
  }
  .btn-lg {
    flex: 1 0 auto;
    text-align: center;
  }
}
</style>