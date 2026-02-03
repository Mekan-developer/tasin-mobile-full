<template>
  <div class="relative min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <NotificationComponent v-if="isNotificationVisible"/>
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md bg-midnight sm:rounded-lg">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
          {{$t('sign_in_title')}}
        </h2>
      </div>
      <div class=" py-8 px-4 shadow  sm:px-10 text-[#cbfaf6]">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-medium">
              {{$t('email_address')}}
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                required
                class="appearance-none block w-full bg-[#e2f8f6] text-[#03524c] px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :placeholder="$t('enter_email')"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium">{{$t('password_label')}}</label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                required
                class="appearance-none bg-[#e2f8f6] text-[#03524c] block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :placeholder="$t('enter_password')"
              />
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="transition transform active:scale-95 group relative w-full flex justify-center py-2
               px-4 border border-transparent text-sm font-medium rounded-md text-white bg-my-gradient hover:bg-my-gradient-reverse focus:outline-none focus:ring-2 cursor-pointer focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg
                  class="animate-spin h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
              </span>
              {{ loading ? $t('signing_in') : $t('sign_in') }}
            </button>
          </div>
        </form>
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
        password: 'password',
      },
      loading: false,
    }
  },
 computed: {
    ...mapGetters('notifications', ['isNotificationVisible']),
  },
  methods: {
    async handleLogin() {
      this.loading = true

      try {
        const result = await AuthController.login(this.form)
        if (result.success)
          this.$router.push('/')
      }finally {
        this.loading = false
      }
    },

  },
}
</script>

