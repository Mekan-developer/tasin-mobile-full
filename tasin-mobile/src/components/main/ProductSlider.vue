<template>
  <div class="slider-container">
    <div
      class="slider-wrapper"
      @touchstart="onTouchStart"
      @touchmove="onTouchMove"
      @touchend="onTouchEnd"
    >
      <div
        class="slides"
        :style="slidesStyle"
      >
        <div v-for="(slide, index) in slides" :key="index" class="slide" :style="{ backgroundColor: slide.bgColor }">
          <div class="slide-content">
            <div class="slide-left">
              <div class="slide-text">
                <span class="badge">{{ slide.badge }}</span>
                <h2 class="title">{{ slide.title }}</h2>
                <div v-if="slide.price != null || slide.discount" class="price-block">
                  <span v-if="slide.discount" class="discount">{{ slide.discount }}</span>
                  <span class="price">{{ formatPriceWithCurrency(slide.price, slide.currency) }}</span>
                  <span v-if="slide.oldPrice != null" class="old-price">{{ formatPriceWithCurrency(slide.oldPrice, slide.currency) }}</span>
                </div>
              </div>
              <p class="description">{{ slide.description }}</p>
            </div>
            <div v-if="hasSlideImage(slide) && !imageLoadFailed[index]" class="slide-image">
              <img
                :src="resolveImagePath(slide.image)"
                :alt="slide.title"
                loading="lazy"
                @error="onImageError(index)"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="slider-indicators">
        <span
          v-for="(slide, index) in slides"
          :key="index"
          class="indicator"
          :class="{ active: currentSlide === index }"
          @click="goToSlide(index)"
        ></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useCategoryStore } from '@/stores/category'
import { formatPriceWithCurrency } from '@/composables/usePriceDisplay'

const categoryStore = useCategoryStore()

/** Путь из API (имя файла) → полный URL для img из assets/img */
function resolveImagePath(path) {
  if (!path) return ''
  const name = path.startsWith('@/') ? path.split('/').pop() : path
  try {
    return new URL(`../../assets/img/${name}`, import.meta.url).href
  } catch {
    return path
  }
}

const currentSlide = ref(0)
let intervalId = null

/** Индексы слайдов, у которых картинка не загрузилась — их блок не показываем */
const imageLoadFailed = ref({})

function onImageError(slideIndex) {
  imageLoadFailed.value = { ...imageLoadFailed.value, [slideIndex]: true }
}

/** Свайп: начальная X при touchstart */
let touchStartX = 0
/** Текущее смещение пальца в px (для визуала при перетаскивании) */
const touchOffset = ref(0)

/** Transform слайдов: базовая позиция (с учётом gap 10px) + смещение свайпа. Без transition при перетаскивании. */
const SLIDE_GAP = 10
const slidesStyle = computed(() => {
  const n = currentSlide.value
  const isDragging = touchOffset.value !== 0
  return {
    transform: `translateX(calc(-${n} * (100% + ${SLIDE_GAP}px) + ${touchOffset.value}px))`,
    transition: isDragging ? 'none' : undefined
  }
})

/** Есть ли у слайда непустое изображение (блок с картинкой не показываем, если нет) */
function hasSlideImage(slide) {
  const img = slide?.image
  return img != null && String(img).trim() !== ''
}


const slides = computed(() => categoryStore.slides)

const goToSlide = (index) => {
  currentSlide.value = index
  resetAutoplay()
}

const nextSlide = () => {
  if (!slides.value?.length) return
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
}

const startAutoplay = () => {
  intervalId = setInterval(nextSlide, 5000)
}

const resetAutoplay = () => {
  if (intervalId) {
    clearInterval(intervalId)
  }
  startAutoplay()
}

/** Минимальная длина свайпа в px для переключения слайда */
const SWIPE_THRESHOLD = 50

function onTouchStart(e) {
  touchStartX = e.touches[0].clientX
  touchOffset.value = 0
}

function onTouchMove(e) {
  const x = e.touches[0].clientX
  const delta = x - touchStartX
  touchOffset.value = delta
}

function onTouchEnd() {
  const delta = touchOffset.value
  touchOffset.value = 0
  if (!slides.value?.length) return
  if (Math.abs(delta) < SWIPE_THRESHOLD) return
  if (delta > 0) {
    currentSlide.value = currentSlide.value === 0 ? slides.value.length - 1 : currentSlide.value - 1
  } else {
    currentSlide.value = (currentSlide.value + 1) % slides.value.length
  }
  resetAutoplay()
}

onMounted(async () => {
  await categoryStore.fetchSlides()
  if (import.meta.env.DEV) {
    console.log('slides', slides.value)
  }
  startAutoplay()
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>

<style scoped>
/* Контейнер слайдера с отступами под все экраны */
.slider-container {
  width: 100%;
  padding: 12px 16px;
  box-sizing: border-box;
}

.slider-wrapper {
  position: relative;
  width: 100%;
  overflow: hidden;
  border-radius: 20px;
  box-shadow: 0 8px 32px var(--shadow-color), 0 2px 8px rgba(0, 0, 0, 0.06);
  touch-action: pan-y;
}

.slides {
  display: flex;
  gap: 10px;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Карточка слайда: мин. высота, градиент поверх фона для глубины */
.slide {
  min-width: 100%;
  min-height: 160px;
  padding: 20px 20px 44px;
  border-radius: 20px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  box-sizing: border-box;
}

.slide::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, transparent 50%);
  pointer-events: none;
}

.slide-content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
}

/** На мобильном: левая колонка (текст + описание); участвует в grid через display: contents */
.slide-left {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.slide-text {
  flex: 1;
  min-width: 0;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}

/* Бейдж: капсула, контрастный фон */
.badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  padding: 4px 10px;
  margin-bottom: 8px;
  text-transform: uppercase;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(6px);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.35);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 6px;
  line-height: 1.25;
  letter-spacing: -0.02em;
}

.description {
  font-size: 13px;
  margin: 0;
  opacity: 0.95;
  line-height: 1.45;
  max-width: 280px;
}

/* Блок цены и скидки */
.price-block {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 8px;
  margin-top: 6px;
}

.price {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.02em;
}

.old-price {
  font-size: 14px;
  opacity: 0.8;
  text-decoration: line-through;
}

.discount {
  font-size: 13px;
  font-weight: 700;
  padding: 2px 8px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 6px;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

/* Изображение товара в слайде */
.slide-image {
  flex-shrink: 0;
  width: 100px;
  height: 100px;
  border-radius: 12px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
}

.slide-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Точки-индикаторы слайдов */
.slider-indicators {
  position: absolute;
  bottom: 14px;
  left: 20px;
  display: flex;
  gap: 8px;
  z-index: 2;
}

.indicator {
  width: 24px;
  height: 4px;
  background-color: rgba(255, 255, 255, 0.4);
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.indicator.active {
  background-color: white;
  width: 32px;
  box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
}

.indicator:hover {
  background-color: rgba(255, 255, 255, 0.7);
}

/* Мобильный: верхняя строка — badge, title, price + картинка; описание ниже (уменьшает высоту) */
@media (max-width: 767px) {
  .slide-content {
    display: grid;
    grid-template-columns: 1fr auto;
    grid-template-rows: auto auto;
    gap: 8px 12px;
    align-items: start;
  }

  .slide-left {
    display: contents;
  }

  .slide-text {
    grid-column: 1;
    grid-row: 1;
  }

  .slide-image {
    grid-column: 2;
    grid-row: 1;
  }

  .description {
    grid-column: 1 / -1;
    grid-row: 2;
    margin: 0;
    max-width: 100%;
  }
}

/* Очень маленькие экраны (< 360px) */
@media (max-width: 359px) {
  .slider-container {
    padding: 10px 12px;
  }

  .slider-wrapper {
    border-radius: 16px;
  }

  .slide {
    min-height: 120px;
    padding: 14px 14px 38px;
    border-radius: 16px;
  }

  .badge {
    font-size: 9px;
    padding: 3px 8px;
    margin-bottom: 4px;
  }

  .title {
    font-size: 16px;
    margin: 0 0 4px;
  }

  .description {
    font-size: 12px;
    max-width: 100%;
  }

  .price-block {
    margin-top: 6px;
    gap: 6px;
  }

  .price {
    font-size: 16px;
  }

  .old-price {
    font-size: 12px;
  }

  .discount {
    font-size: 11px;
    padding: 2px 6px;
  }

  .slide-image {
    width: 64px;
    height: 64px;
    border-radius: 10px;
  }

  .slider-indicators {
    bottom: 10px;
    left: 14px;
    gap: 6px;
  }

  .indicator {
    width: 20px;
    height: 3px;
  }

  .indicator.active {
    width: 26px;
  }
}

/* Телефоны 360–767px */
@media (min-width: 360px) and (max-width: 767px) {
  .slide {
    min-height: 130px;
    padding: 16px 16px 42px;
  }

  .title {
    font-size: 18px;
  }

  .description {
    font-size: 13px;
  }

  .slide-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
  }
}

/* Планшеты и выше */
@media (min-width: 768px) {
  .slider-container {
    padding: 16px 24px;
  }

  .slide {
    min-height: 200px;
    padding: 28px 32px 52px;
  }

  .badge {
    font-size: 11px;
    padding: 5px 12px;
    margin-bottom: 10px;
  }

  .title {
    font-size: 22px;
  }

  .description {
    font-size: 15px;
    max-width: 400px;
    margin: 0;
  }

  .price-block {
    margin-top: 12px;
  }

  .price {
    font-size: 20px;
  }

  .old-price {
    font-size: 15px;
  }

  .discount {
    font-size: 14px;
  }

  .slide-image {
    width: 120px;
    height: 120px;
    border-radius: 14px;
  }

  .slider-indicators {
    bottom: 18px;
    left: 32px;
    gap: 10px;
  }

  .indicator {
    width: 28px;
    height: 4px;
  }

  .indicator.active {
    width: 40px;
  }
}

/* Десктоп */
@media (min-width: 1024px) {
  .slider-container {
    padding: 20px 32px 24px;
  }

  .slide {
    min-height: 220px;
    padding: 36px 40px 60px;
  }

  .badge {
    margin-bottom: 12px;
  }

  .title {
    font-size: 26px;
    margin: 0 0 8px;
  }

  .description {
    font-size: 16px;
    max-width: 480px;
  }

  .price {
    font-size: 22px;
  }

  .slide-image {
    width: 140px;
    height: 140px;
    border-radius: 16px;
  }

  .slider-indicators {
    bottom: 24px;
    left: 40px;
  }

  .indicator {
    width: 32px;
    height: 5px;
  }

  .indicator.active {
    width: 48px;
  }
}

/* Широкий десктоп */
@media (min-width: 1280px) {
  .slider-container {
    padding: 24px 48px 28px;
  }

  .slide {
    min-height: 240px;
    padding: 44px 48px 68px;
  }

  .title {
    font-size: 28px;
  }

  .description {
    font-size: 17px;
    max-width: 520px;
  }

  .price {
    font-size: 24px;
  }

  .slide-image {
    width: 160px;
    height: 160px;
  }
}
</style>
