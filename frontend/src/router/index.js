import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import GoToSignUpView from '@/views/GoToSignUpView.vue'
import UserSignUpView from '@/views/UserSignUpView.vue'
import EmployerSignUpView from '@/views/EmployerSignUpView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: GoToSignUpView,
    },
    {
      path: '/user/select-role',
      name: 'select_role',
      component: GoToSignUpView,
    },
    {
      path: '/candidate/signup',
      name: 'candidate_signup',
      component: UserSignUpView,
      props: { role: 'candidate' },
    },
    {
      path: '/admin/signup',
      name: 'admin_signup',
      component: UserSignUpView,
      props: { role: 'admin' },
    },
    {
      path: '/employer/signup',
      name: 'employer_signup',
      component: EmployerSignUpView,
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

export default router
