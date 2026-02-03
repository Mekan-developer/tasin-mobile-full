<template>
    <div v-for="lang in languages" :key="lang" class="w-full flex justify-center cursor-pointer">
        <div @click="switchLang(lang)" :class="{'bg-gradient-to-br  via-blue-900 from-[#00020ecc] to-deepblue ': $i18n.locale == lang}" class="flex items-center justify-center text-white hover:bg-gradient-to-br  hover:via-blue-900 hover:from-[#00020ecc] hover:to-deepblue  rounded-md p-2 transition-colors duration-300">
            <AppIcon name="Languages" size="10" class="inline-block mr-2" />
            <span  class="cursor-pointer">{{ lang.toUpperCase() }}</span>
        </div>
    </div>
</template>

<script>
export default {
  name: "LanguageSwitcher",
  data() {
    return {
      // Берём список доступных языков из i18n
      languages: this.$i18n.availableLocales
    }
  },
  methods: {
    switchLang(lang) {
      this.$i18n.locale = lang
      localStorage.setItem('lang', lang)
    }
  },
  mounted() {
    const saved = localStorage.getItem('lang')
    if (saved && this.languages.includes(saved)) {
      this.$i18n.locale = saved
    }
  }
}
</script>
