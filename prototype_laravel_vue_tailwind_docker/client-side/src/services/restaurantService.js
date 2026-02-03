// client-side/src/services/restaurantService.js
import api from '@/api/http';

export default {
  // Получить все рестораны (super-admin)
  async getAll() {
    const response = await api.get('/restaurants');
    return response.data;
  },

  // Получить мой ресторан (owner)
  async getMy() {
    const response = await api.get('/restaurants/my');
    return response.data;
  },

  // Создать ресторан с владельцем
  async create(restaurantData, ownerData) {
    const response = await api.post('/restaurants', {
      restaurant: restaurantData,
      owner: ownerData
    });
    return response.data;
  },

  // Обновить ресторан
  async update(id, data) {
    const response = await api.put(`/restaurants/${id}`, data);
    return response.data;
  },

  // Удалить ресторан
  async delete(id) {
    const response = await api.delete(`/restaurants/${id}`);
    return response.data;
  },

  // Получить работников ресторана
  async getEmployees(restaurantId) {
    const response = await api.get(`/restaurants/${restaurantId}/employees`);
    return response.data;
  }
};
