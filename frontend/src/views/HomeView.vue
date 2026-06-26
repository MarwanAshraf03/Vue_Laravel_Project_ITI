<script setup>
import { onMounted, ref } from 'vue'
import { getCurrentUser } from '@/services/userService'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import AdminHomeView from './AdminHomeView.vue'

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
  <AdminHomeView v-if="currentUser && currentUser.role === 'admin'" />
  <main v-else class="admin-shell">
    <section class="admin-card">
      <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
          <p class="eyebrow">Admin dashboard</p>
          <h1>Welcome back{{ currentUser ? `, ${currentUser.name}` : '' }}</h1>
          <p class="muted">You are signed in as an administrator.</p>
        </div>
        <button class="logout-btn" @click="logout">Logout</button>
      </div>

      <div class="summary-grid" v-if="currentUser">
        <article>
          <span>Name</span>
          <strong>{{ currentUser.name }}</strong>
        </article>
        <article>
          <span>Email</span>
          <strong>{{ currentUser.email }}</strong>
        </article>
        <article>
          <span>Role</span>
          <strong>{{ currentUser.role }}</strong>
        </article>
      </div>
    </section>
  </main>
</template>

<style scoped>
.admin-shell {
  min-height: 100vh;
  padding: 2rem 1.25rem 3rem;
  background: radial-gradient(circle at top, #eaf2ff 0, #f8f9ff 45%, #eef4ff 100%);
}

.admin-card {
  max-width: 1120px;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.9);
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
}

h1 {
  margin: 0;
  color: #0b1c30;
}

.muted {
  color: #5f6b7c;
  margin-top: 0.5rem;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin: 1.75rem 0;
}

.summary-grid article {
  background: #f8fbff;
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 18px;
  padding: 1rem;
}

.summary-grid span {
  display: block;
  font-size: 0.8rem;
  color: #6b7280;
  margin-bottom: 0.4rem;
}

.summary-grid strong {
  color: #0b1c30;
  word-break: break-word;
}

.logout-btn {
  border: none;
  border-radius: 999px;
  background: #0b1c30;
  color: #fff;
  padding: 0.85rem 1.4rem;
}
</style>
