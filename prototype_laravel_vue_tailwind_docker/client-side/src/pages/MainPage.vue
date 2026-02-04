<template>
    <div>
        <div class="h-[calc(100%-2rem)] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-fogactive scrollbar-track-deepblue">
            <!-- Демо контент как на скриншоте -->
            <div class="space-y-6">
              <!-- Welcome Section -->
              <div class="bg-gradient-to-r from-midnight/50 to-deepblue/50 p-6 rounded-xl border border-fog/20">
                <h1 class="text-2xl font-bold text-white mb-2">Main</h1>
                <p class="text-lg text-fog">Добро пожаловать в панель управления</p>
              </div>

              <!-- Dashboard Section -->
              <div class="bg-gradient-to-r from-midnight/50 to-deepblue/50 p-6 rounded-xl border border-fog/20">
                <h2 class="text-xl font-bold text-white mb-4">Dashboard</h2>
                <div class="space-y-3">
                  <p class="text-gray-200">- Welcome to Dashboard</p>
                  <p class="text-gray-300 ml-4">- Here goes your content... test</p>
                  <p class="text-yellow-400 ml-8 text-2xl">- 🎉🎉🎉</p>
                </div>
              </div>

              <!-- Divider -->
              <div class="border-t border-fog/20 my-4"></div>

              <!-- Settings Section -->
              <div class="bg-gradient-to-r from-midnight/50 to-deepblue/50 p-6 rounded-xl border border-fog/20">
                <h2 class="text-xl font-bold text-white mb-4">Settings</h2>
                <div class="space-y-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-md">
                      МР
                    </div>
                    <div>
                      <p class="text-lg font-semibold text-white">Мекан Раззаков</p>
                      <div class="mt-2">
                        <p class="text-sm text-gray-400">API Key:</p>
                        <div class="flex items-center gap-2 mt-1">
                          <code class="bg-black/40 px-3 py-2 rounded text-gray-300 font-mono text-sm">API_KEY_1234567890</code>
                          <button class="p-1.5 hover:bg-fogactive rounded">
                            <AppIcon name="Copy" size="16" class="text-gray-400" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Stats -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-gradient-to-br from-midnight to-deepblue p-4 rounded-xl border border-fog/20">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-fog">Активные пользователи</p>
                      <p class="text-2xl font-bold text-white mt-2">1,234</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                      <AppIcon name="Users" size="20" class="text-indigo-400" />
                    </div>
                  </div>
                </div>

                <div class="bg-gradient-to-br from-midnight to-deepblue p-4 rounded-xl border border-fog/20">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-fog">Запросы сегодня</p>
                      <p class="text-2xl font-bold text-white mt-2">5,678</p>
                    </div>
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                      <AppIcon name="Activity" size="20" class="text-green-400" />
                    </div>
                  </div>
                </div>

                <div class="bg-gradient-to-br from-midnight to-deepblue p-4 rounded-xl border border-fog/20">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-fog">Сервера онлайн</p>
                      <p class="text-2xl font-bold text-white mt-2">12/12</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                      <AppIcon name="Server" size="20" class="text-blue-400" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
    </div>
</template>

<script>
export default {
  name: "MainPage",
};
</script>
