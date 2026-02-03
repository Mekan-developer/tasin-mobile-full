<template>
  <div class="not-found-container">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
      <!-- Основное содержимое -->
      <div class="p-8 md:p-12">
        <div class="text-center fade-in">
          <div class="float mb-6">
            <div class="relative inline-block">
              <div class="bg-red-100 w-32 h-32 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-exclamation-triangle text-red-500 text-5xl"></i>
              </div>
              <div class="absolute -inset-4 bg-red-100 rounded-full opacity-50 -z-10"></div>
              <div class="absolute -inset-6 bg-red-100 rounded-full opacity-30 -z-20"></div>
            </div>
          </div>

          <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">404</h1>
          <h2 class="text-2xl md:text-3xl font-semibold text-gray-700 mb-6">Страница не найдена</h2>
          <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Извините, но страница, которую вы ищете, не существует. Возможно, она была перемещена или удалена.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
              <i class="fas fa-arrow-left mr-2"></i> Назад
            </button>
            <button @click="goHome" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
              <i class="fas fa-home mr-2"></i> На главную
            </button>
          </div>
        </div>
      </div>

      <!-- Дополнительная информация -->
      <div class="bg-gray-50 p-6 text-center">
        <p class="text-gray-600 mb-4">Нужна помощь? Свяжитесь с нами</p>
        <div class="flex justify-center space-x-4">
          <a href="#" class="text-indigo-600 hover:text-indigo-800 transition duration-300">
            <i class="fab fa-telegram text-xl"></i>
          </a>
          <a href="#" class="text-indigo-600 hover:text-indigo-800 transition duration-300">
            <i class="fas fa-envelope text-xl"></i>
          </a>
          <a href="#" class="text-indigo-600 hover:text-indigo-800 transition duration-300">
            <i class="fas fa-phone-alt text-xl"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Быстрые ссылки -->
    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
      <a v-for="(link, index) in quickLinks" :key="index" :href="link.url" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition duration-300">
        <i :class="link.icon + ' text-indigo-600 text-xl mb-2'"></i>
        <p class="text-sm text-gray-700">{{ link.text }}</p>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFoundPage',
  data() {
    return {
      quickLinks: [
        { icon: 'fas fa-box', text: 'Продукты', url: '#' },
        { icon: 'fas fa-newspaper', text: 'Блог', url: '#' },
        { icon: 'fas fa-tag', text: 'Акции', url: '#' },
        { icon: 'fas fa-info-circle', text: 'О нас', url: '#' }
      ]
    }
  },
  methods: {
    goBack() {
      window.history.back();
    },
    goHome() {
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
.not-found-container {
  width: 100%;
  max-width: 42rem;
  margin: 0 auto;
}

.fade-in {
  animation: fadeIn 0.8s ease-in-out;
}

.float {
  animation: float 3s ease-in-out infinite;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0px); }
}
</style>
