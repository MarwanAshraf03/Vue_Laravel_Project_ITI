<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import Cookies from 'js-cookie'
import { fetchJob } from '@/services/jobsService'
import { getCurrentUser } from '@/services/userService'
import { fetchJobComments, postComment, deleteComment } from '@/services/commentService'
import ApplyModal from '@/components/ApplyModal.vue'

const route = useRoute()
const router = useRouter()
const job = ref(null)
const loading = ref(true)
const isModalOpen = ref(false)
const currentUser = ref(null)
const comments = ref([])
const newComment = ref('')
const commentLoading = ref(false)
const commentError = ref('')
const backendBase = 'http://localhost:8000'

onMounted(async () => {
  currentUser.value = await getCurrentUser()
  const result = await fetchJob(route.params.id)
  if (result.ok) {
    job.value = result.data
    await loadComments()
  }
  loading.value = false
})

async function loadComments() {
  const res = await fetchJobComments(route.params.id)
  if (res.ok) comments.value = res.data
}

async function submitComment() {
  if (!newComment.value.trim()) return
  commentLoading.value = true
  commentError.value = ''
  const res = await postComment(route.params.id, newComment.value)
  commentLoading.value = false
  if (res.ok) {
    newComment.value = ''
    await loadComments()
  } else {
    commentError.value = res.message || 'Failed to post comment.'
  }
}

async function handleDeleteComment(id) {
  const res = await deleteComment(id)
  if (res.ok) await loadComments()
}

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

function logoUrl(path) {
  if (!path) return null
  if (path.startsWith('http')) return path
  return `${backendBase}/storage/${path}`
}

function formatWorkType(wt) {
  if (!wt) return ''
  return wt.charAt(0).toUpperCase() + wt.slice(1)
}

function techList(techs) {
  if (!techs) return []
  return techs.split(',').map((t) => t.trim()).filter(Boolean)
}

function requirementsList(reqs) {
  if (!reqs) return []
  return reqs.split('\n').map((r) => r.trim()).filter(Boolean)
}

function benefitsList(bens) {
  if (!bens) return []
  return bens.split('\n').map((b) => b.trim()).filter(Boolean)
}
</script>

<template>
  <main class="candidate-shell">
    <nav class="candidate-nav">
      <div>
        <RouterLink to="/candidate/home" class="back-link">← Back to Jobs</RouterLink>
        <p class="brand">Jobify</p>
      </div>
      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <div v-if="loading" class="loading-state">Loading job details...</div>
    <div v-else-if="!job" class="error-state">Job not found.</div>
    <div v-else class="job-details-wrapper">
      <article class="job-detail-card">

        <!-- Header: Logo + Title -->
        <div class="job-header">
          <div class="header-top">
            <img
              v-if="logoUrl(job.company_logo)"
              :src="logoUrl(job.company_logo)"
              class="company-logo"
              alt="Company Logo"
            />
            <div class="header-text">
              <h1>{{ job.title }}</h1>
              <span class="company-name">{{ job.company }}</span>
            </div>
          </div>

          <div class="job-meta">
            <span class="meta-pill" v-if="job.location">📍 {{ job.location }}</span>
            <span class="meta-pill work-type">🖥️ {{ formatWorkType(job.work_type) }}</span>
            <span class="meta-pill" v-if="job.salary_min || job.salary_max">
              💰 ${{ job.salary_min }} – ${{ job.salary_max }}
            </span>
            <span class="meta-pill" v-if="job.experience_level">🎓 {{ job.experience_level }}</span>
            <span class="meta-pill" v-if="job.category">🏷️ {{ job.category }}</span>
            <span class="meta-pill" v-if="job.industry">🏢 {{ job.industry }}</span>
            <span class="meta-pill deadline" v-if="job.deadline">📅 Deadline: {{ job.deadline }}</span>
          </div>
        </div>

        <!-- Tech Stack -->
        <div v-if="techList(job.technologies).length" class="section">
          <h2>Technologies</h2>
          <div class="tech-tags">
            <span v-for="tech in techList(job.technologies)" :key="tech" class="tech-tag">{{ tech }}</span>
          </div>
        </div>

        <!-- Description -->
        <div class="section">
          <h2>Job Description</h2>
          <p class="prose">{{ job.description }}</p>
        </div>

        <!-- Requirements -->
        <div v-if="requirementsList(job.requirements).length" class="section">
          <h2>Requirements</h2>
          <ul class="bullets">
            <li v-for="(req, i) in requirementsList(job.requirements)" :key="i">{{ req }}</li>
          </ul>
        </div>

        <!-- Benefits -->
        <div v-if="benefitsList(job.benefits).length" class="section">
          <h2>Benefits</h2>
          <ul class="bullets benefits-list">
            <li v-for="(ben, i) in benefitsList(job.benefits)" :key="i">{{ ben }}</li>
          </ul>
        </div>

        <!-- Employer Contact -->
        <div v-if="job.employer_profile || job.user" class="section contact-section">
          <h2>Employer Contact</h2>
          <div class="contact-info">
            <p v-if="job.employer_profile?.company_name || job.user?.name">
              <strong>Name:</strong> {{ job.employer_profile?.company_name || job.user?.name }}
            </p>
            <p v-if="job.employer_profile?.website">
              <strong>Website:</strong>
              <a :href="job.employer_profile.website" target="_blank" class="contact-link">
                {{ job.employer_profile.website }}
              </a>
            </p>
            <p v-if="job.employer_profile?.phone">
              <strong>Phone:</strong> {{ job.employer_profile.phone }}
            </p>
            <p v-if="job.user?.email">
              <strong>Email:</strong>
              <a :href="`mailto:${job.user.email}`" class="contact-link">{{ job.user.email }}</a>
            </p>
          </div>
        </div>

        <!-- Apply Button -->
        <button v-if="currentUser?.role === 'candidate'" class="apply-btn" @click="isModalOpen = true">
          Apply Now
        </button>
      </article>

      <!-- Comments Section -->
      <section class="comments-section">
        <h2>Comments</h2>

        <!-- Post a comment (logged in users only) -->
        <div v-if="currentUser" class="comment-form">
          <textarea
            v-model="newComment"
            placeholder="Write a comment..."
            rows="3"
          ></textarea>
          <div class="comment-form-actions">
            <span v-if="commentError" class="comment-error">{{ commentError }}</span>
            <button @click="submitComment" :disabled="commentLoading" class="post-btn">
              {{ commentLoading ? 'Posting...' : 'Post Comment' }}
            </button>
          </div>
        </div>
        <p v-else class="login-to-comment">
          <RouterLink :to="{ name: 'user_login' }">Log in</RouterLink> to leave a comment.
        </p>

        <div v-if="comments.length === 0" class="no-comments">No comments yet. Be the first!</div>
        <div v-else class="comments-list">
          <div v-for="comment in comments" :key="comment.id" class="comment-card">
            <div class="comment-header">
              <span class="comment-author">{{ comment.user?.name || 'Anonymous' }}</span>
              <span class="comment-date">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
            </div>
            <p class="comment-body">{{ comment.content }}</p>
            <button
              v-if="currentUser && (currentUser.id === comment.user_id || currentUser.role === 'admin')"
              class="delete-comment-btn"
              @click="handleDeleteComment(comment.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </section>
    </div>

    <ApplyModal v-if="job" :job="job" :is-open="isModalOpen" @close="isModalOpen = false" />
  </main>
</template>

<style scoped>
.candidate-shell {
  min-height: 100%;
  padding: 1.25rem;
  background:
    radial-gradient(circle at top left, rgba(0, 106, 97, 0.16), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.18), transparent 28%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
}

.candidate-nav,
.job-details-wrapper {
  max-width: 900px;
  margin: 0 auto;
}

.candidate-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 22px;
  box-shadow: 0 18px 50px rgba(11, 28, 48, 0.08);
}

.back-link {
  display: block;
  margin-bottom: 0.25rem;
  color: #0b1c30;
  text-decoration: none;
  font-weight: 600;
}

.brand {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.nav-link,
.logout-btn {
  border-radius: 999px;
  padding: 0.75rem 1.1rem;
  text-decoration: none;
  font-weight: 700;
}

.nav-link {
  color: #0b1c30;
  background: #f3f7ff;
  border: 1px solid rgba(11, 28, 48, 0.08);
}

.logout-btn {
  border: none;
  background: #0b1c30;
  color: #fff;
  cursor: pointer;
}

/* Card */
.job-detail-card {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
  margin-bottom: 2rem;
}

/* Header */
.job-header {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(11, 28, 48, 0.08);
}

.header-top {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-bottom: 1.25rem;
}

.company-logo {
  width: 72px;
  height: 72px;
  object-fit: contain;
  border-radius: 12px;
  border: 1px solid rgba(11, 28, 48, 0.1);
  background: #f3f7ff;
  flex-shrink: 0;
}

.header-text h1 {
  margin: 0 0 0.3rem 0;
  font-size: 2rem;
}

.company-name {
  display: block;
  font-size: 1.1rem;
  color: #5f6b7c;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.meta-pill {
  background: #f3f7ff;
  padding: 0.4rem 0.9rem;
  border-radius: 999px;
  font-size: 0.88rem;
  color: #0b1c30;
  border: 1px solid rgba(11, 28, 48, 0.08);
}

.meta-pill.work-type {
  background: #e7f4f3;
  color: #006a61;
  border-color: rgba(0, 106, 97, 0.2);
}

.meta-pill.deadline {
  background: #fff7e6;
  color: #92400e;
  border-color: rgba(146, 64, 14, 0.2);
}

/* Sections */
.section {
  margin-bottom: 2rem;
}

.section h2 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-top: 0;
  margin-bottom: 0.75rem;
  color: #0b1c30;
}

.prose {
  line-height: 1.7;
  color: #334155;
  font-size: 1rem;
  margin: 0;
  white-space: pre-wrap;
}

.bullets {
  margin: 0;
  padding-left: 1.5rem;
  color: #334155;
  line-height: 1.8;
}

.benefits-list li::marker {
  content: '✅ ';
}

/* Tech tags */
.tech-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tech-tag {
  background: #e7f4f3;
  color: #006a61;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid rgba(0, 106, 97, 0.2);
}

/* Contact */
.contact-section {
  background: #f7fbff;
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
}

.contact-info p {
  margin: 0.4rem 0;
  color: #334155;
}

.contact-link {
  color: #006a61;
  text-decoration: none;
  font-weight: 600;
}

.contact-link:hover {
  text-decoration: underline;
}

/* Apply btn */
.apply-btn {
  margin-top: 2rem;
  padding: 1rem 2rem;
  background: #0b1c30;
  color: white;
  border: none;
  border-radius: 999px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.apply-btn:hover {
  background: #1a3a5c;
}

/* Comments */
.comments-section {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
  margin-bottom: 2rem;
}

.comments-section h2 {
  margin-top: 0;
  font-size: 1.3rem;
  color: #0b1c30;
  margin-bottom: 1.25rem;
}

.comment-form textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid rgba(11, 28, 48, 0.2);
  border-radius: 12px;
  font-size: 0.95rem;
  resize: vertical;
  box-sizing: border-box;
  font-family: inherit;
}

.comment-form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
}

.comment-error {
  color: #dc2626;
  font-size: 0.9rem;
}

.post-btn {
  background: #0b1c30;
  color: white;
  border: none;
  padding: 0.65rem 1.5rem;
  border-radius: 999px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.post-btn:hover {
  background: #1a3a5c;
}

.post-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-to-comment {
  color: #5f6b7c;
  margin-bottom: 1rem;
}

.login-to-comment a {
  color: #006a61;
  font-weight: 600;
  text-decoration: none;
}

.no-comments {
  color: #5f6b7c;
  text-align: center;
  padding: 2rem;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.comment-card {
  background: #f7fbff;
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  position: relative;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.comment-author {
  font-weight: 700;
  color: #0b1c30;
}

.comment-date {
  font-size: 0.8rem;
  color: #5f6b7c;
}

.comment-body {
  margin: 0;
  color: #334155;
  line-height: 1.6;
}

.delete-comment-btn {
  margin-top: 0.5rem;
  background: none;
  border: none;
  color: #dc2626;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
}

.delete-comment-btn:hover {
  text-decoration: underline;
}

/* States */
.loading-state,
.error-state {
  text-align: center;
  padding: 4rem;
  color: #5f6b7c;
  font-size: 1.2rem;
}

@media (max-width: 720px) {
  .candidate-nav {
    flex-direction: column;
    align-items: flex-start;
  }

  .nav-actions {
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .header-top {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
