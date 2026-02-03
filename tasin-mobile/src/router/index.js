import { createWebHistory, createRouter } from 'vue-router'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: () => import('@/pages/MainPage.vue')
    },
    {
      path: '/category/:id',
      component: () => import('@/pages/ProductPage.vue'),
      meta:{
        hideHeader: true
      }
    },
    {
      path: '/gallery',
      component: () => import('@/pages/GalleryPage.vue')
    },
    {
      path: '/cart',
      component: () => import('@/pages/CartPage.vue')
    },
    {
      path: '/profile',
      component: () => import('@/pages/ProfilePage.vue')
    }
  ]
})
