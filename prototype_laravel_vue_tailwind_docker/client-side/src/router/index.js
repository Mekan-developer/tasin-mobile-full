import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/auth/Login.vue'
import store from '../store'

const routes = [
   {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/',
    // redirect: '/login',
    name: 'Dashboard',
    component: () => import('../layouts/Dashboard.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '/',
        name: 'Main',
        component: () => import('../pages/MainPage.vue'),
        meta: { title: "main page", requiresAuth: true },
      },
      {
        path: '/home',
        name: 'Home',
        component: () => import('../pages/HomePage.vue'),
        meta: { title: "home page", requiresAuth: true },
      },
      {
        path: '/categories',
        name: 'Categories',
        component: () => import('../pages/categories/IndexPage.vue'),
        meta: { title: "Categories page", requiresAuth: true },
      },
      {
        path: '/slides',
        name: 'Slides',
        component: () => import('../pages/slides/SlidesPage.vue'),
        meta: { title: "Slides page", requiresAuth: true },
      },
      {
        path: '/products',
        name: 'Products',
        component: () => import('../pages/products/IndexPage.vue'),
        meta: { title: "Products page", requiresAuth: true },
      },
      {
        path: '/settings',
        name: 'Settings',
        component: () => import('../pages/SettingPage.vue'),
        meta: { title: "setting page", requiresAuth: true },
      },

      {
        path: '/tables',
        name: 'Tables',
        component: () => import('../pages/TablePage.vue'),
        meta: { title: "table page", requiresAuth: true },
      },
      {
        path: '/dropdown',
        meta: { requiresAuth: true },
        children: [
          {
            path: 'page1',
            name: 'dropdown1',
            component: () => import('../pages/DropDown1.vue'),
            meta: { title: "Dropdown Page 1" },
          },
          {
            path: 'page2',
            name: 'dropdown2',
            component: () => import('../pages/DropDown2.vue'),
            meta: { title: "Dropdown Page 2" },
          }
        ]
      },
      {
        path: '/users',
        name: 'UsersLayout',
        component: () => import('../layouts/UsersLayout.vue'),
        meta: { title: "user page", requiresAuth: true },
        children: [
          {
            path: '',
            name: 'users',
            component: () => import('../pages/users/IndexPage.vue'),
            meta: { title: "user page", requiresAuth: true  },
          },
        ]
      },
    ],
  },
  {
    path: '/server-error',
    name: 'ServerError',
    component: () => import('../pages/errorsPage/ServerError.vue'),
    meta: { title: 'Ошибка сервера' }
  },
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component:  () => import('../pages/errorsPage/notFound.vue'),
    meta: { title: "page not found" },
  }

]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  document.title = to.meta.title || "Default title"

  // Не проверяем сервер для страницы ошибки
  if (to.path === '/server-error') {
    return next();
  }

  try {
    // Проверяем доступность сервера перед любыми действиями
    const isServerOnline = await store.dispatch('server/checkServerStatus');
    if (!isServerOnline) {
      // Если сервер недоступен, перенаправляем на страницу ошибки
      return next('/server-error');
    }

    const isAuth = await store.dispatch('auth/checkAuth');

    if (to.meta.requiresAuth && !isAuth) return next('/login')
    if (to.path === '/login' && isAuth) return next('/')

    // // Проверка роли, если маршрут требует конкретные роли
    // if (to.meta.roles && Array.isArray(to.meta.roles) && to.meta.roles.length > 0) {
    //   const storedUser = store.state.user.currentUser || JSON.parse(localStorage.getItem('user') || 'null')
    //   const userRole = storedUser?.role
    //   if (!userRole || !to.meta.roles.includes(userRole)) {
    //     return next('/home')
    //   }
    // }

    next()
  } catch (error) {
      console.error('Router navigation error:', error);
      next('/server-error');
    }
})

export default router
