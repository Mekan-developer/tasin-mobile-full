<template>
  <div>
    <label :for="id" class="whitespace-nowrap">{{ label }}</label>
    <select
      class="custom-input"
      :id="id"
      v-model="localValue"
      @change="onChange"
    >
      <option class="bg-[#001f3f]"
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  name: "FormSelect",
  props: {
    modelValue: { type: String, default: "" },
    label: { type: String, default: "" },
    id: { type: String, default: "select" },
    options: {
      type: Array,
      required: true
      // формат: [{ value: 'manager', label: 'Manager' }, { value: 'user', label: 'User' }]
    }
  },
  data() {
    return {
      localValue: this.modelValue
    }
  },
  watch: {
    modelValue(val) {
      this.localValue = val
    }
  },
  methods: {
    onChange(e) {
      this.$emit("update:modelValue", e.target.value)
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

</style>
