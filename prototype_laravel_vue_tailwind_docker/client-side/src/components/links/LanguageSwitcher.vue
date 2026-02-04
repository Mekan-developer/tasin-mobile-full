<template>
  <div class="language-switcher flex items-center gap-2 ">
    <!-- Language Buttons -->
    <div
      v-for="lang in languages"
      :key="lang"
      class="language-option group relative"
    >
      <button
        @click="switchLang(lang)"
        :class="[
          'language-button flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-200',
          isActive(lang) ? 'active-language' : 'inactive-language'
        ]"
        :title="$t('switch_to', { lang: lang.toUpperCase() })"
      >
        <!-- Flag/Language Icon -->
        <AppIcon
          :name="getLanguageIcon(lang)"
          size="14"
          :class="[
            'transition-colors duration-200',
            isActive(lang) ? 'text-white' : 'text-fog group-hover:text-white'
          ]"
        />

        <!-- Language Code -->
        <span
          :class="[
            'text-xs font-medium transition-colors duration-200',
            isActive(lang) ? 'text-white' : 'text-gray-400 group-hover:text-white'
          ]"
        >
          {{ lang.toUpperCase() }}
        </span>
      </button>

      <!-- Active Indicator -->
      <div
        v-if="isActive(lang)"
        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-activeColor rounded-full"
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LanguageSwitcher",
  data() {
    return {
      languages: this.$i18n.availableLocales
    }
  },
  methods: {
    switchLang(lang) {
      this.$i18n.locale = lang
      localStorage.setItem('lang', lang)
      // Optional: emit event for parent component
      this.$emit('language-changed', lang)
    },

    isActive(lang) {
      return this.$i18n.locale === lang
    },

    getLanguageIcon(lang) {
      const icons = {
        'en': 'Flag',
        'ru': 'Flag',
        'tm': 'Flag',
        'us': 'Flag',
        'gb': 'Flag'
      }
      return icons[lang] || 'Languages'
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

<style scoped>
.language-button {
  border: 1px solid transparent;
  min-width: 56px;
}

.inactive-language {
  background: rgba(71, 89, 129, 0.1);
  border-color: rgba(207, 207, 209, 0.1);
}

.inactive-language:hover {
  background: rgba(71, 89, 129, 0.3);
  border-color: rgba(71, 89, 129, 0.5);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.active-language {
  background: linear-gradient(135deg, rgba(0, 31, 63, 0.9), rgba(24, 28, 54, 0.9));
  border-color: rgba(0, 31, 63, 0.5);
  box-shadow: 0 2px 6px rgba(0, 31, 63, 0.3);
}

.language-button:active {
  transform: scale(0.95);
}

/* Compact mode for collapsed sidebar */


.language-switcher:has(.language-button:only-child) .language-button {
  padding: 8px;
  min-width: 40px;
}
</style>
