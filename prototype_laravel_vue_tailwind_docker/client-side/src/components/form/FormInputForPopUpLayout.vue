<template>
  <div>
    <label :for="id" class="text-[14px]">{{ placeholder }}</label>
    <input
      :id="id"
      :type="type"
      v-model="localValue"
      :placeholder="placeholder"
      class="custom-input"
      :class="{ 'is-valid': isValid, 'is-invalid': !isValid && localValue }"
      @input="onInput"
    />
    <p v-if="!isValid && localValue" class="text-red-500 text-sm mt-1">
      {{ error }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'FormInput',
  props: {
    id: { type: String, default: "input" },
    modelValue: { type: String, default: '' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    error: { type: String, default: 'Ошибка' },
    validator: { type: Function, required: true }
  },
  data() {
    return {
      localValue: this.modelValue,
      isValid: true
    }
  },
  watch: {
    modelValue(val) {
      this.localValue = val
      this.isValid = this.validator(val)
    }
  },
  methods: {
    onInput(e) {
      const val = e.target.value
      this.localValue = val
      this.isValid = this.validator(val)
      this.$emit('update:modelValue', val)
    }
  }
}
</script>
<style scoped>

.custom-input {
  width: 100%;
  padding: 10px 6px;
  background-color: rgba(68, 110, 224, 0.1); /* Светлый синий фон */
  border: none;
  border-bottom: 2px solid #617ca5; /* Серый цвет границы */
  border-radius: 4px 4px 0 0; /* Скругление только сверху */
  font-size: 14px; /* Увеличенный шрифт */
  color: #ffffff; /* Темный текст для контрастности */
  transition: all 0.3s ease; /* Плавные переходы */
  outline: none;
}

.custom-input:focus {
  border-bottom-color: #446ee0; /* Синий при фокусе */
  background-color: rgba(68, 110, 224, 0.05);
  box-shadow: 0 2px 4px rgba(141, 105, 241, 0.568); /* Тень при фокусе */
}

.custom-input::placeholder {
  color: #ffffffc4; /* Серый цвет плейсхолдера */
  opacity: 1;
}

.is-valid {
  border-bottom-color: #4e5af7; /* Зеленый для валидного ввода */
}

.is-invalid {
  border-bottom-color: #ef4444; /* Красный для невалидного ввода */
}
</style>
