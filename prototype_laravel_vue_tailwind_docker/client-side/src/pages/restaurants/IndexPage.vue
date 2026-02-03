<template>
  <div>
    <div v-if="errorMessage" class="mb-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded-sm p-3">
      {{ errorMessage }}
    </div>
    <DataTableCard
      title="Restaurants"
      :rows="restaurants"
      :columns="columns"
      idKey="id"
      :pageSize="10"
      @create="onCreate"
      @edit="onEdit"
      @delete="onDelete"
    />

    <!-- Модальное окно создания -->
    <PopUpLayout
      :is-visible-pop-up-layout="isVisiblePopUpLayout"
      :mounted="mounted"
      :title="isEditing ? 'Edit Restaurant' : 'Create Restaurant & Owner'"
      @toggle-visible-pop-up-layout="toggleVisiblePopUpLayout"
      @onAfterLeave="handleAfterLeave"
    >
      <template #content>
        <div v-if="errorMessage" class="mb-3 text-sm text-red-100 bg-red-900/40 border border-red-600 rounded-sm p-3">
          {{ errorMessage }}
        </div>
        <RestaurantForm
          :form="form"
          :is-editing="isEditing"
          :submit-label="isEditing ? 'Update restaurant' : 'Create restaurant'"
          @submit="onSubmit"
          @cancel="toggleVisiblePopUpLayout"
        />
      </template>
    </PopUpLayout>
  </div>
</template>

<script>
  import DataTableCard from '@/components/tables/DataTableCard.vue';
  import PopUpLayout from '@/components/popUpLayout.vue';
  import RestaurantForm from '@/components/restaurants/RestaurantForm.vue';
  import restaurantService from '@/services/restaurantService';
  import { popUpLayoutMixins } from '@/mixins/popUpLayoutMixins';

  export default {
    name: "RestaurantsIndexPage",
    components: {
      DataTableCard,
      PopUpLayout,
      RestaurantForm
    },
    mixins: [popUpLayoutMixins],
    data() {
      return {
        restaurants: [],
        loading: false,
        errorMessage: '',
        isEditing: false,
        editingId: null,
        form: {
          restaurant: {
            name: '',
            description: '',
            address: '',
            phone: '',
            active: true
          },
          owner: {
            name: '',
            email: '',
            password: ''
          }
        },
        columns: [
          { key: "name", label: "Name" },
          { key: "address", label: "Address" },
          { key: "phone", label: "Phone" },
          { key: "owner", label: "Owner", format: (value) => value?.name || '—' }
        ]
      };
    },
    async mounted() {
      await this.loadRestaurants();
    },
    methods: {
      async loadRestaurants() {
        try {
          this.loading = true;
          const response = await restaurantService.getAll();
          if (response.success) {
            this.restaurants = response.data || [];
          }
        } catch (error) {
          console.error('Error loading restaurants:', error);
        } finally {
          this.loading = false;
        }
      },
      onCreate() {
        this.errorMessage = '';
        this.isEditing = false;
        this.editingId = null;
        this.form = {
          restaurant: { name: '', description: '', address: '', phone: '', active: true },
          owner: { name: '', email: '', password: '' }
        };
        this.toggleVisiblePopUpLayout();
      },
      async onSubmit() {
        // Предварительная проверка на клиенте, чтобы не слать пустые поля
        if (!this.validateForm()) {
          return;
        }

        try {
          this.loading = true;
          this.errorMessage = '';
          let response;
          if (this.isEditing && this.editingId) {
            response = await restaurantService.update(this.editingId, this.form.restaurant);
          } else {
            response = await restaurantService.create(
              this.form.restaurant,
              this.form.owner
            );
          }

          if (response.success) {
            await this.loadRestaurants();
            this.toggleVisiblePopUpLayout();
            // Показать уведомление об успехе
          } else {
            console.error('Error saving restaurant:', response.error || response.message);
            this.errorMessage = response.error || response.message || 'Failed to save restaurant';
          }
        } catch (error) {
          console.error('Error saving restaurant:', error);
          // Детализируем ошибки 4xx
          if (error.response?.status === 422) {
            const errors = error.response.data?.errors || {};
            this.errorMessage = Object.entries(errors)
              .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
              .join('\n');
          } else if (error.response?.status === 403) {
            this.errorMessage = this.isEditing
              ? 'Access denied. Only super-admin can update restaurants.'
              : 'Access denied. Only super-admin can create restaurants.';
          } else if (error.response?.status === 400) {
            this.errorMessage = error.response.data?.message || error.response.data?.error || 'Failed to save restaurant';
          } else {
            this.errorMessage = error.response?.data?.message || error.message || 'Unknown error';
          }
        } finally {
          this.loading = false;
        }
      },
      onEdit(row) {
        this.errorMessage = '';
        this.isEditing = true;
        this.editingId = row.id;
        this.form = {
          restaurant: {
            name: row.name || '',
            description: row.description || '',
            address: row.address || '',
            phone: row.phone || '',
            active: row.active !== false,
          },
          // При редактировании владельца не меняем
          owner: { name: '', email: '', password: '' }
        };
        this.toggleVisiblePopUpLayout();
      },
      async onDelete(row) {
        if (confirm(`Delete restaurant "${row.name}"?`)) {
          try {
            await restaurantService.delete(row.id);
            await this.loadRestaurants();
          } catch (error) {
            console.error('Error deleting restaurant:', error);
          }
        }
      },

      validateForm() {
        if (!this.form.restaurant.name || this.form.restaurant.name.length < 2) {
          this.errorMessage = 'Restaurant name is required (min 2 chars)';
          return false;
        }
        if (!this.form.restaurant.address || this.form.restaurant.address.length < 5) {
          this.errorMessage = 'Restaurant address is required (min 5 chars)';
          return false;
        }
        // При редактировании не требуем данных владельца
        if (!this.isEditing) {
          if (!this.form.owner.name || this.form.owner.name.length < 2) {
            this.errorMessage = 'Owner name is required (min 2 chars)';
            return false;
          }
          const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!this.form.owner.email || !emailRe.test(this.form.owner.email)) {
            this.errorMessage = 'Valid owner email is required';
            return false;
          }
          if (!this.form.owner.password || this.form.owner.password.length < 8) {
            this.errorMessage = 'Owner password is required (min 8 chars)';
            return false;
          }
        }
        this.errorMessage = '';
        return true;
      },
      handleAfterLeave() {
        this.onAfterLeave();
        // Сброс режима редактирования
        if (!this.isVisiblePopUpLayout) {
          this.isEditing = false;
          this.editingId = null;
        }
      }
    }
  };
  </script>
