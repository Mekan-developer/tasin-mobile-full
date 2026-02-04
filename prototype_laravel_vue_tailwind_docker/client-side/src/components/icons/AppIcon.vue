<template>
  <component
    v-if="iconComponent"
    :is="iconComponent"
    v-bind="iconProps"
    :class="[
      'inline-flex items-center justify-center flex-shrink-0',
      'transition-all duration-150',
      sizeClass,
      customClass
    ]"
    :style="iconStyles"
    :aria-label="ariaLabel"
    :title="title || name"
    role="img"
  />
  <div
    v-else
    :class="[
      'inline-flex items-center justify-center text-gray-400',
      'transition-all duration-150',
      sizeClass,
      customClass
    ]"
    :style="iconStyles"
    :aria-label="ariaLabel"
    :title="title || `Icon ${name} not found`"
    role="img"
  >
    <span class="text-xs font-medium">?</span>
  </div>
</template>

<script>
import * as icons from "lucide-vue-next"
import { computed } from 'vue'

export default {
  name: "AppIcon",
  props: {
    name: {
      type: String,
      required: true,
      validator: (value) => {
        // Базовая валидация - не пустая строка
        return typeof value === 'string' && value.trim().length > 0
      }
    },
    size: {
      type: [String, Number],
      default: 20,
      validator: (value) => {
        const num = Number(value)
        return !isNaN(num) && num > 0 && num <= 100
      }
    },
    color: {
      type: String,
      default: "currentColor"
    },
    strokeWidth: {
      type: [String, Number],
      default: 2,
      validator: (value) => {
        const num = Number(value)
        return !isNaN(num) && num >= 0.5 && num <= 5
      }
    },
    fill: {
      type: String,
      default: "none"
    },
    spin: {
      type: Boolean,
      default: false
    },
    pulse: {
      type: Boolean,
      default: false
    },
    rotate: {
      type: [String, Number],
      default: 0,
      validator: (value) => {
        const num = Number(value)
        return !isNaN(num) && num >= 0 && num <= 360
      }
    },
    flip: {
      type: String,
      default: null,
      validator: (value) => {
        return !value || ['horizontal', 'vertical', 'both'].includes(value)
      }
    },
    customClass: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    ariaLabel: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    // Находим иконку или возвращаем заглушку
    const iconComponent = computed(() => {
      if (!props.name) return null

      // Пробуем разные варианты написания
      const name = props.name
      let component = icons[name]

      // Если не найдено, пробуем с разными форматами
      if (!component) {
        // Попробуем с префиксом Lucide
        const lucideName = name.startsWith('Lucide') ? name : `Lucide${name}`
        component = icons[lucideName]

        // Попробуем все в lowercase
        if (!component) {
          const lowerName = name.toLowerCase()
          component = Object.keys(icons).find(key =>
            key.toLowerCase() === lowerName ||
            key.toLowerCase().replace(/^lucide/, '') === lowerName
          )
          if (component) component = icons[component]
        }
      }

      return component || null
    })

    // Определяем классы размера
    const sizeClass = computed(() => {
      const sizes = {
        12: 'w-3 h-3',
        14: 'w-3.5 h-3.5',
        16: 'w-4 h-4',
        18: 'w-[18px] h-[18px]',
        20: 'w-5 h-5',
        24: 'w-6 h-6',
        28: 'w-7 h-7',
        32: 'w-8 h-8',
        36: 'w-9 h-9',
        40: 'w-10 h-10',
        48: 'w-12 h-12',
        56: 'w-14 h-14',
        64: 'w-16 h-16'
      }

      const size = Number(props.size)
      return sizes[size] || `w-[${size}px] h-[${size}px]`
    })

    // Собираем стили для иконки
    const iconStyles = computed(() => {
      const styles = {}

      // Если есть кастомный цвет
      if (props.color && props.color !== 'currentColor') {
        styles.color = props.color
      }

      // Анимация вращения
      if (props.spin) {
        styles.animation = 'spin 1s linear infinite'
      }

      if (props.pulse) {
        styles.animation = 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite'
      }

      // Поворот
      if (props.rotate) {
        const rotate = Number(props.rotate)
        styles.transform = `rotate(${rotate}deg)`
      }

      // Отразить
      if (props.flip) {
        const flipMap = {
          horizontal: 'scaleX(-1)',
          vertical: 'scaleY(-1)',
          both: 'scale(-1)'
        }
        styles.transform = `${styles.transform || ''} ${flipMap[props.flip]}`.trim()
      }

      return styles
    })

    // Пропсы для передачи в lucide компонент
    const iconProps = computed(() => ({
      size: Number(props.size),
      color: props.color,
      strokeWidth: Number(props.strokeWidth),
      fill: props.fill,
      'aria-hidden': !props.ariaLabel // скрыть от скринридеров если нет aria-label
    }))

    // Заглушка для не найденных иконок
    const fallbackIcon = computed(() => {
      return icons.HelpCircle || icons.AlertCircle || icons.QuestionMark
    })

    return {
      iconComponent: iconComponent.value || fallbackIcon,
      iconProps,
      sizeClass,
      iconStyles,
      fallbackIcon
    }
  },

  // Методы для иконок, которые могут понадобиться
  methods: {
    // Получить список доступных иконок
    getAvailableIcons() {
      return Object.keys(icons).filter(name =>
        typeof icons[name] === 'function' &&
        !name.startsWith('Lucide') &&
        name !== 'default'
      )
    },

    // Проверить существование иконки
    iconExists(iconName) {
      return !!icons[iconName]
    },

    // Получить иконку по имени
    getIconComponent(iconName) {
      return icons[iconName] || icons.HelpCircle
    }
  }
}
</script>

<style scoped>
/* Анимации для иконок */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Улучшение доступности */
[role="img"]:focus {
  outline: 2px solid currentColor;
  outline-offset: 2px;
  border-radius: 2px;
}

/* Плавные переходы для hover эффектов */
:deep(svg) {
  transition: all 0.2s ease-in-out;
}

/* Состояния для разных тем */
@media (prefers-color-scheme: dark) {
  .icon-dark {
    color: #e5e7eb;
  }
}

@media (prefers-color-scheme: light) {
  .icon-light {
    color: #374151;
  }
}


/* Принтер стили */
@media print {
  :deep(svg) {
    fill: black !important;
    stroke: black !important;
  }
}
</style>
