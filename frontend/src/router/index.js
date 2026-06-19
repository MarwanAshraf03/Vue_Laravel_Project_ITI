import { createRouter, createWebHistory } from 'vue-router'
import GoToSignUpView from '@/views/GoToSignUpView.vue'
import UserSignUpView from '@/views/UserSignUpView.vue'
import EmployerSignUpView from '@/views/EmployerSignUpView.vue'
import LoginView from '@/views/LoginView.vue'
import CandidateHomeView from '@/views/CandidateHomeView.vue'
import CandidateProfileView from '@/views/CandidateProfileView.vue'
import JobDetailsView from '@/views/JobDetailsView.vue'
import HomeView from '@/views/HomeView.vue'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'
import EmployerHomeView from '@/views/EmployerHomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { auth: true, role: 'admin' },
    },
    {
      path: '/candidate/home',
      name: 'candidate_home',
      component: CandidateHomeView,
      meta: { auth: true, role: 'candidate' },
      alias: ['/candidate', '/candidate/'],
    },
    {
      path: '/candidate/jobs/:id',
      name: 'job_details',
      component: JobDetailsView,
      meta: { auth: true, role: 'candidate' },
    },
    {
      path: '/candidate/profile',
      name: 'candidate_profile',
      component: CandidateProfileView,
      meta: { auth: true, role: 'candidate' },
    },
    {
      path: '/user/select-role',
      name: 'select_role',
      component: GoToSignUpView,
      meta: { auth: false },
      alias: '/user/choose-role',
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
      path: '/employer/home',
      name: 'employer_home',
      component: EmployerHomeView,
      meta: { auth: true, role: 'employer' },
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

router.beforeEach(async (to) => {
  const auth = to.meta.auth
  const token = Cookies.get('token')

  if (auth && !token) {
    return { name: 'user_login' }
  }

  if (token) {
    const activeUser = await getCurrentUser()

    if (!activeUser) {
      return { name: 'user_login' }
    }

    if (!auth) {
      return activeUser.role === 'candidate' ? { name: 'candidate_home' } : { name: 'home' }
    }

    if (to.meta.role && activeUser.role !== to.meta.role) {
      console.log('line 112')
      // return activeUser.role === 'candidate' ?  : { name: 'home' }
      switch (to.meta.role) {
        case 'candidate':
          return { name: 'candidate_home' }
        case 'employer':
          console.log('line 118')
          return { name: 'employer_home' }

        default:
          break
      }
    }
    if (to.path === '/' && activeUser.role === "employer")
      return { name: 'employer_home' }
  }
  console.log({ to })
  console.log('line 126')

  return
})

export default router
