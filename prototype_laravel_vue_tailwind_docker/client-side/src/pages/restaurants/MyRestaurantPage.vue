<template>
  <div>
    <div v-if="loading" class="text-center py-8">Loading...</div>
    <div v-else-if="restaurant" class="space-y-4">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">{{ restaurant.name }}</h2>
        <div class="space-y-2">
          <p><strong>Address:</strong> {{ restaurant.address }}</p>
          <p v-if="restaurant.phone"><strong>Phone:</strong> {{ restaurant.phone }}</p>
          <p v-if="restaurant.description"><strong>Description:</strong> {{ restaurant.description }}</p>
        </div>
      </div>

      <!-- Список работников -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4">Employees</h3>
        <div v-if="employees.length === 0" class="text-gray-500">No employees yet</div>
        <div v-else class="space-y-2">
          <div v-for="employee in employees" :key="employee.id" class="border-b pb-2">
            <p><strong>{{ employee.name }}</strong> - {{ employee.email }}</p>
            <p class="text-sm text-gray-500">Role: {{ employee.role }}</p>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8 text-red-500">
      Restaurant not found
    </div>
  </div>
</template>

<script>
import restaurantService from '@/services/restaurantService';

export default {
  name: 'MyRestaurantPage',
  data() {
    return {
      restaurant: null,
      employees: [],
      loading: false
    };
  },
  async mounted() {
    await this.loadMyRestaurant();
  },
  methods: {
    async loadMyRestaurant() {
      try {
        this.loading = true;
        const response = await restaurantService.getMy();
        if (response.success) {
          this.restaurant = response.data;
          this.employees = response.data.users || [];
        }
      } catch (error) {
        console.error('Error loading restaurant:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
