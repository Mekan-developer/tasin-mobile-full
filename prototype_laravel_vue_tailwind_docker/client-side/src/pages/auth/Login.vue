<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-cyan-50 flex items-center justify-center p-4 sm:p-6">
    <!-- Notification Overlay -->
    <transition name="slide-down">
      <NotificationComponent
        v-if="isNotificationVisible"
        class="absolute top-4 left-4 right-4 sm:left-auto sm:right-4 sm:top-4 sm:w-96 z-50"
      />
    </transition>

    <!-- Main Card -->
    <div class="w-full max-w-md">
      <!-- Logo/Brand Area (Optional) -->
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-r from-cyan-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $t('sign_in_title') }}</h1>
        <p class="text-gray-600 mt-2 text-sm">Введите данные для входа в аккаунт</p>
      </div>

      <!-- Login Form Card -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Decorative Top Bar -->
        <div class="h-2 bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500"></div>

        <div class="p-6 sm:p-8">
          <form class="space-y-6" @submit.prevent="handleLogin">
            <!-- Email Input -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                {{ $t('email_address') }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                  :placeholder="$t('enter_email')"
                />
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-medium text-gray-900">
                  {{ $t('password_label') }}
                </label>
                <a href="#" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium">
                  Забыли пароль?
                </a>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                  :placeholder="$t('enter_password')"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center"
                  @click="togglePasswordVisibility"
                >
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Remember Me (Optional) -->
            <div class="flex items-center">
              <input
                id="remember-me"
                type="checkbox"
                class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Запомнить меня
              </label>
            </div>

            <!-- Submit Button -->
            <div>
              <button
                type="submit"
                :disabled="loading"
                class="relative w-full flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl"
              >
                <span class="relative flex items-center">
                  <svg
                    v-if="loading"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ loading ? $t('signing_in') : $t('sign_in') }}
                </span>
              </button>
            </div>

            <!-- Divider -->
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Или войдите через</span>
              </div>
            </div>


          </form>

          <!-- Sign Up Link -->
          <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
              Нет аккаунта?
              <a href="#" class="font-medium text-cyan-600 hover:text-cyan-700 ml-1">
                Зарегистрироваться
              </a>
            </p>
          </div>
        </div>
      </div>

      <!-- Version/Bottom Text -->
      <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">© {{ thisYear }} Tasin Mobile Project. Все права защищены.</p>
        <p class="text-xs text-gray-500 mt-1">Версия 1.0.0</p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import AuthController from '../../controllers/AuthController'
import NotificationComponent from '@/components/notifications/NotificationComponent.vue'

export default {
  name: 'UserLogin',
  components: { NotificationComponent },
  data() {
    return {
      form: {
        email: 'mekan.developer@gmail.com',
        password: '12345678',
      },
      loading: false,
      showPassword: false
    }
  },
  computed: {
    ...mapGetters('notifications', ['isNotificationVisible']),
    thisYear() {
      return new Date().getFullYear()
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true

      try {
        const result = await AuthController.login(this.form)
        if (result.success) {
          this.$router.push('/')
        }
      } finally {
        this.loading = false
      }
    },

    togglePasswordVisibility() {
      this.showPassword = !this.showPassword
      const passwordInput = document.getElementById('password')
      if (passwordInput) {
        passwordInput.type = this.showPassword ? 'text' : 'password'
      }
    }
  },
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
  transform: translateY(0);
}

.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

/* Better focus styles for mobile */
input:focus {
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
}

/* Improve button tap feedback on mobile */
@media (max-width: 640px) {
  button:active {
    transform: scale(0.98);
  }

  .shadow-lg {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }
}

/* Smooth transitions for all interactive elements */
button, input, a {
  transition: all 0.2s ease-in-out;
}
</style>
