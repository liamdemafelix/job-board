import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/job/:id',
      name: 'job',
      component: () => import('@/views/JobView.vue')
    },
    {
      path: '/jobs/create',
      name: 'create-job',
      component: () => import('@/views/CreateJobView.vue')
    }
  ],
})

export default router
