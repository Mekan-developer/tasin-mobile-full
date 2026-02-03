import api from '../../api/http'


const state = () => ({
  token: localStorage.getItem('access_token') || '',
  status: 'idle',
  error: '',
})

const getters = {
  isAuthenticated: (state) => {
    if (!state.token) return false;
      return true;
  },
  authStatus: (state) => state.status,
}

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token
  },
  SET_STATUS(state, status) {
    state.status = status
  },
  SET_ERROR(state, error) {
    state.error = error
  },
}

const actions = {

   async checkAuth({ getters, commit, dispatch }) {
    if (!getters.isAuthenticated) return false;

    try {
      // Делаем запрос к защищенному эндпоинту для валидации токена
      const response = await api.get('/me');

      // Если запрос успешен, токен валиден
      if (response.data && response.data.success && response.data.data) {
        // Обновляем данные пользователя в store
        const userData = response.data.data;
        await dispatch('user/setUser', userData, { root: true });

        return true;
      }

      return false;
    } catch (error) {
      // Если токен невалиден (401) или другие ошибки
      if (error.response) {
        const status = error.response.status;

        // 401 - токен истёк или невалиден
        // 403 - доступ запрещён
        if (status === 401 || status === 403) {
          // Очищаем токен и данные пользователя
          commit('SET_TOKEN', '');
          localStorage.removeItem('access_token');
          await dispatch('user/setUser', null, { root: true });
          return false;
        }
      }

      // Для других ошибок (сеть, сервер и т.д.) возвращаем false
      console.error('Auth check failed:', error);
      return false;
    }
  },

  async refresh({ commit }) {
    const response = await api.post('/refresh', {}, { withCredentials: true })
    commit('SET_TOKEN', response.data.token)
  },

  setToken({ commit }, token) {
    commit('SET_TOKEN', token)
    if (token) {
      localStorage.setItem('access_token', token)
    } else {
      localStorage.removeItem('access_token')
    }
  },
  setStatus({ commit }, status) {
    commit('SET_STATUS', status)
  },
  setError({ commit }, error) {
    commit('SET_ERROR', error)
  },
  logout({ commit }) {
    commit('SET_TOKEN', '')
    localStorage.removeItem('access_token')
    localStorage.removeItem('user')
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
