import api from '../../api/http'


// store/modules/server.js
export default {
  namespaced: true,
  state: {
    isOnline: true,
    lastChecked: null
  },
  mutations: {
    SET_SERVER_STATUS(state, status) {
      state.isOnline = status;
      state.lastChecked = new Date();
    }
  },
  actions: {
    async checkServerStatus({ commit }) {
      try {
        // Отправляем запрос к простому эндпоинту здоровья сервера
        const response = await api.get('/status');

        const isOnline = response.data.success;
        commit('SET_SERVER_STATUS', isOnline);
        return isOnline;
      } catch (error) {
        console.error('Server status check failed:', error);
        commit('SET_SERVER_STATUS', false);
        return false;
      }
    }
  }
}
