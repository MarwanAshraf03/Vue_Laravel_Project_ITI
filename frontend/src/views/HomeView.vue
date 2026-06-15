<script setup>
import { currentUser } from '../services/userService'
import AdminHomeView from './AdminHomeView.vue'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'

const router = useRouter()
function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}
</script>

<template>
  <button @click="logout">Logout</button>
  <h1>{{ currentUser }}</h1>
  <AdminHomeView v-if="currentUser.role === 'admin'" />
</template>
