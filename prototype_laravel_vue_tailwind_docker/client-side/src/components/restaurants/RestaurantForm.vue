<template>
  <div class="flex flex-col gap-6 text-slate-100 text-sm max-w-3xl w-full mx-auto">
    <div class="space-y-1">
      <h3 class="text-lg font-semibold text-white">Создать ресторан и владельца</h3>
      <p class="text-xs text-slate-300">
        Заполните данные заведения и сразу создайте владельца с доступами.
      </p>
    </div>

    <!-- Ресторан -->
    <div class="rounded-md border border-slate-600/60 bg-slate-800/80 shadow-[0_10px_32px_-14px_rgba(0,0,0,0.65)] p-4 sm:p-5 space-y-3">
      <div class="flex items-center justify-between gap-2">
        <div>
          <h4 class="text-sm font-semibold text-white">Ресторан</h4>
          <p class="text-[11px] text-slate-400">Основные данные заведения</p>
        </div>
        <span class="text-[11px] px-2 py-1 rounded-sm bg-indigo-700/40 text-indigo-100 uppercase tracking-wide">Обязательные</span>
      </div>

      <FormInput
        v-model="form.restaurant.name"
        id="restaurant.name"
        type="text"
        placeholder="Название ресторана"
        :error="'Restaurant name is required'"
        :validator="validateName"
      />

      <FormInput
        v-model="form.restaurant.address"
        id="restaurant.address"
        type="text"
        placeholder="Адрес"
        :error="'Address is required'"
        :validator="validateAddress"
      />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <FormInput
          v-model="form.restaurant.phone"
          id="restaurant.phone"
          type="tel"
          placeholder="Телефон (необязательно)"
          :error="''"
          :validator="() => true"
        />
        <div class="flex flex-col">
          <label for="restaurant.description" class="text-[11px] font-medium text-slate-300 mb-1">Описание</label>
          <textarea
            id="restaurant.description"
            v-model="form.restaurant.description"
            placeholder="Короткое описание (необязательно)"
            class="custom-input min-h-[82px]"
          ></textarea>
        </div>
      </div>

      <div class="flex items-center justify-between pt-1">
        <div class="text-[11px] text-slate-300">Доступ к системе</div>
        <FormCheckboxToggle
          v-model="form.restaurant.active"
          :label="'Активен'"
        />
      </div>
    </div>

    <!-- Владелец -->
    <div
      v-if="!isEditing"
      class="rounded-md border border-slate-600/60 bg-slate-800/80 shadow-[0_10px_32px_-14px_rgba(0,0,0,0.65)] p-4 sm:p-5 space-y-3"
    >
      <div class="flex items-center justify-between gap-2">
        <div>
          <h4 class="text-sm font-semibold text-white">Владелец</h4>
          <p class="text-[11px] text-slate-400">Создадим аккаунт владельца</p>
        </div>
        <span class="text-[11px] px-2 py-1 rounded-sm bg-indigo-700/40 text-indigo-100 uppercase tracking-wide">Обязательные</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <FormInput
          v-model="form.owner.name"
          id="owner.name"
          type="text"
          placeholder="Имя владельца"
          :error="'Owner name is required'"
          :validator="validateOwnerName"
        />

        <FormInput
          v-model="form.owner.email"
          id="owner.email"
          type="email"
          placeholder="Email владельца"
          :error="'Valid email is required'"
          :validator="validateEmail"
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <FormInput
          v-model="form.owner.password"
          id="owner.password"
          type="password"
          placeholder="Пароль (мин. 8 символов)"
          :error="'Password must be at least 8 characters'"
          :validator="validatePassword"
        />
        <div class="flex items-center text-xs text-slate-300 leading-relaxed">
          Пароль будет задан владельцу. Минимум 8 символов, лучше — с цифрой и символом.
        </div>
      </div>
    </div>

    <div class="w-full flex flex-col sm:flex-row sm:justify-end items-stretch sm:items-center gap-3 mt-2">
      <button
        type="button"
        class="w-full sm:w-auto px-4 py-2 rounded-sm border border-slate-600 text-slate-100 hover:bg-slate-750 hover:bg-slate-700/70 transition"
        @click="$emit('cancel')"
      >
        Cancel
      </button>
      <div class="w-full sm:w-auto">
        <SubmitButton @click="$emit('submit')" :buttonText="submitLabelComputed"/>
      </div>
    </div>
  </div>
</template>

<script>
  import FormInput from '@/components/form/FormInputForPopUpLayout.vue';
  import FormCheckboxToggle from "@/components/form/FormCheckboxToggle.vue";
  import SubmitButton from '@/components/form/SubmitButton.vue';

  export default {
    name: 'RestaurantForm',
    components: {
      FormInput,
      FormCheckboxToggle,
      SubmitButton
    },
    props: {
      form: {
        type: Object,
        required: true,
        default: () => ({
          restaurant: { name: '', description: '', address: '', phone: '', active: true },
          owner: { name: '', email: '', password: '' }
        })
      },
      isEditing: {
        type: Boolean,
        default: false
    },
    submitLabel: {
      type: String,
      default: 'Create restaurant'
      }
    },
  computed: {
    submitLabelComputed() {
      return this.submitLabel || (this.isEditing ? 'Update restaurant' : 'Create restaurant');
    }
  },
    methods: {
      validateName(value) {
        return value && value.length >= 2;
      },
      validateAddress(value) {
        return value && value.length >= 5;
      },
      validateOwnerName(value) {
        return value && value.length >= 2;
      },
      validateEmail(value) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(value);
      },
      validatePassword(value) {
        return value && value.length >= 8;
      }
    }
  };
  </script>
