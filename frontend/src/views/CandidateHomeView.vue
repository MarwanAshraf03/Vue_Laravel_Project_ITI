<script setup>
import { onMounted, ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'

const router = useRouter()
const currentUser = ref(null)

onMounted(async () => {
  currentUser.value = await getCurrentUser()
})

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}
</script>

<template>
  <main class="candidate-shell">
    <nav class="candidate-nav">
      <div>
        <p class="brand">Jobify</p>
        <small class="muted">Candidate workspace</small>
      </div>

      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <section class="hero-card">
      <p class="eyebrow">Welcome{{ currentUser ? `, ${currentUser.name}` : '' }}</p>
      <h1>Your candidate dashboard is ready.</h1>
      <p>
        Keep your profile current, update your headline, and manage your account from the profile
        page.
      </p>

      <div class="quick-grid" v-if="currentUser">
        <article>
          <span>Name</span>
          <strong>{{ currentUser.name }}</strong>
        </article>
        <article>
          <span>Email</span>
          <strong>{{ currentUser.email }}</strong>
        </article>
        <article>
          <span>Headline</span>
          <strong>{{ currentUser.job_title || 'No headline set yet' }}</strong>
        </article>
      </div>
    </section>
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
.hero-card {
  max-width: 1120px;
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

.brand {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
}

.muted {
  color: #5f6b7c;
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
}

.hero-card {
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
}

.eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.75rem;
  color: #006a61;
  margin-bottom: 0.5rem;
  font-weight: 800;
}

.hero-card h1 {
  margin: 0;
  font-size: clamp(2rem, 4vw, 3.4rem);
}

.hero-card p {
  max-width: 60ch;
  color: #5f6b7c;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-top: 1.5rem;
}

.quick-grid article {
  background: #f8fbff;
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 18px;
  padding: 1rem;
}

.quick-grid span {
  display: block;
  color: #5f6b7c;
  font-size: 0.85rem;
  margin-bottom: 0.35rem;
}

.quick-grid strong {
  word-break: break-word;
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
}
</style>