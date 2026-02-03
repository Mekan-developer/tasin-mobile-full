<template>
  <div>
    <div class="text-white rounded-sm shadow-[0px_14px_34px_20px_rgba(12,8,22,0.1)]">
      <DataTableCard
        title="Users"
        :rows="items"
        :columns="cols"
        idKey="id"
        :pageSize="8"
        @create="onCreate"
        @edit="onEdit"
        @delete="onDelete"
      />
    </div>

    <PopUpLayout :is-visible-pop-up-layout="isVisiblePopUpLayout" :mounted="mounted"
      :title="$t('user.user_create_page')" @toggle-visible-pop-up-layout="isVisiblePopUpLayout = false" @onAfterLeave="onAfterLeave"
      >
      <template #content>

        <div class="flex flex-col gap-4">
          <FormInput v-model="form.name" id="user.name"
            type="text" :placeholder="$t('user.name')"
            :error="$t('user.name_error')" :validator="validateName" />

          <FormInput v-model="form.email" id="user.email"
            type="email" :placeholder="$t('user.email')"
            :error="$t('user.email_error')" :validator="validateEmail" />

          <FormInput v-model="form.password" id="user.password"
            type="password" :placeholder="$t('user.password')"
            :error="$t('user.password_error')" :validator="validatePassword" />

          <FormSelectOptionForPopUpLayout
            v-model="form.role"
            :label="$t('user.select_role')"
            id="role"
            :options="[
              { value: 'manager', label: 'Manager' },
              { value: 'user', label: 'User' }
            ]"
          />

          <div class="w-full flex justify-end items-center">
              <div class="flex justify-between items-center w-full">
                <div>
                  <FormCheckboxToggle v-model="form.isActive" :label="$t('user.active_status')" />
                </div>
                 <SubmitButton @click="onSubmit" :buttonText="$t('user.user_create')"/>
              </div>
          </div>
        </div>
      </template>
    </popUpLayout>
  </div>
</template>

<script>
import FormCheckboxToggle from "@/components/form/FormCheckboxToggle.vue";
import FormInput from "@/components/form/FormInputForPopUpLayout.vue";
import FormSelectOptionForPopUpLayout from "@/components/form/FormSelectOptionForPopUpLayout.vue";
import SubmitButton from "@/components/form/SubmitButton.vue";
import PopUpLayout from "@/components/popUpLayout.vue";
import DataTableCard from "@/components/tables/DataTableCard.vue";
import userService from "@/services/userService";
import {popUpLayoutMixins} from "@/mixins/popUpLayoutMixins";
export default{
  name: "UsersPage",
  components: { DataTableCard,PopUpLayout,FormInput,FormSelectOptionForPopUpLayout,FormCheckboxToggle,SubmitButton },
  mixins: [popUpLayoutMixins],
  data() {
    return {
      loading: false,
      errorMessage: "",
      form: {
        name: '', email: '', password: '', role: 'manager'},
      cols: [
        { key: "name", label: "Name" },
        { key: "email", label: "Email" },
        { key: "role", label: "Role" },
        { key: "restaurant", label: "Restaurant" },
        { key: "updated_at", label: "Updated" },
      ],
      items: [],
    };
  },

  async mounted() {
    await this.loadUsers();
  },

  computed: {
    // isValidName() {
    //   return this.form.name.length >= 3; // Валидация: имя ≥ 3 символов
    // },
    // isValidEmail() {
    //   return this.form.email.includes('@');
    // }
  },
  methods: {

    async onSubmit() {
      // TODO: реализовать создание сотрудника
      this.toggleVisiblePopUpLayout();
    },
    async loadUsers() {
      try {
        this.loading = true;
        this.errorMessage = "";
        const response = await userService.getAll();
        if (response.success) {
          this.items = (response.data || []).map((u) => ({
            id: u.id,
            name: u.name,
            email: u.email,
            role: u.roles?.[0]?.name || u.role || "—",
            restaurant: u.restaurant?.name || "—",
            updated_at: u.updated_at ? u.updated_at.slice(0, 10) : "",
          }));
        } else {
          this.errorMessage = response.error || "Failed to load users";
        }
      } catch (error) {
        console.error("Error loading users:", error);
        this.errorMessage = error.response?.data?.message || error.message || "Failed to load users";
      } finally {
        this.loading = false;
      }
    },
    validateInput() {
      // Вызывается при каждом изменении input
      this.$emit('input', this.form.name);
    },
    onCreate() {
      this.toggleVisiblePopUpLayout();
     },
    onEdit(row) { alert("Редактировать: " + row.name); },
    onDelete(row) { alert("Удалить: " + row.name); },

    validateName(value) {
      return value.length > 3
    },
    validateEmail(value) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return re.test(value)
    },
    validatePassword(value) {
      // минимум 8 символов, хотя бы одна заглавная буква, одна цифра и один спецсимвол
      const re = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{4,}$/
      return re.test(value)
    }

  },
}
</script>

<style scoped>


</style>
