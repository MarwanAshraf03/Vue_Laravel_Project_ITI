import { createRouter, createWebHistory } from 'vue-router'
import GoToSignUpView from '@/views/GoToSignUpView.vue'
import UserSignUpView from '@/views/UserSignUpView.vue'
import EmployerSignUpView from '@/views/EmployerSignUpView.vue'
import LoginView from '@/views/LoginView.vue'
import HomeView from '@/views/HomeView.vue'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { auth: true },
    },
    {
      path: '/user/select-role',
      name: 'select_role',
      component: GoToSignUpView,
      meta: { auth: false },
    },
    {
      path: '/user/login',
      name: 'user_login',
      component: LoginView,
      meta: { auth: false },
    },
    {
      path: '/candidate/signup',
      name: 'candidate_signup',
      component: UserSignUpView,
      props: { role: 'candidate' },
      meta: { auth: false },
    },
    {
      path: '/admin/signup',
      name: 'admin_signup',
      component: UserSignUpView,
      props: { role: 'admin' },
      meta: { auth: false },
    },
    {
      path: '/employer/signup',
      name: 'employer_signup',
      component: EmployerSignUpView,
      meta: { auth: false },
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
  ],
})

router.beforeEach(async (to, from) => {
  const auth = to.meta.auth
  const token = Cookies.get('token')

  if (auth && !token) {
    return { name: 'user_login' }
  } else if (!auth && token) {
    await getCurrentUser()
    return { name: 'home' }
  } else {
    await getCurrentUser()
    return
  }
})

export default router
