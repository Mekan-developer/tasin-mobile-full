const state = () => ({
  currentUser: JSON.parse(localStorage.getItem('user') || 'null'),
  status: 'idle',
  error: '',
})

const getters = {
  currentUser: (state) => state.currentUser,
}

const mutations = {
  SET_USER(state, user) {
    state.currentUser = user
  },
  SET_STATUS(state, status) {
    state.status = status
  },
  SET_ERROR(state, error) {
    state.error = error
  },
}

const actions = {
  setUser({ commit }, user) {
    commit('SET_USER', user)
    if (user) {
      localStorage.setItem('user', JSON.stringify(user))
    } else {
      localStorage.removeItem('user')
    }
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
