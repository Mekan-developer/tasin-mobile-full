const state = () => ({
    dropdownOpen: false,
    collapseSidebar: false,
    loading: false,
})

const mutations = {
    TOGGLE_DROPDOWN(state) {
        state.dropdownOpen = !state.dropdownOpen
        if(state.dropdownOpen){
            state.collapseSidebar = false
        }
    },
    SET_DROPDOWN(state, value) {
        state.dropdownOpen = value
    },
    TOGGLE_COLLAPSE(state) {
        state.collapseSidebar = !state.collapseSidebar
        if(state.collapseSidebar){
            state.dropdownOpen = false
        }
    },
    SET_COLLAPSE(state, value) {
        state.collapseSidebar = value
    },
    SET_LOADING(state, value) {
        state.loading = value
    },
    START_LOADING(state) {
      state.loading = true
    },
    STOP_LOADING(state) {
        state.loading = false
    }
}

const actions = {
    toggleDropdown({ commit }) {
        commit('TOGGLE_DROPDOWN')
    },
    setDropdown({ commit }, value) {
        commit('SET_DROPDOWN', value)
    },
    toggleCollapse({ commit }) {
        commit('TOGGLE_COLLAPSE')
    },
    setCollapse({commit}, value){
        commit('SET_COLLAPSE', value)
    },
    setLoading({commit}, value){
        commit('SET_LOADING', value)
    },
    startLoading({ commit }) {
        commit('START_LOADING')
    },
    stopLoading({ commit }) {
        commit('STOP_LOADING')
    }
}

const getters = {
    isDropdownOpen: (state) => state.dropdownOpen,
    isCollapsed: (state) => state.collapseSidebar,
    loading: (state) => state.loading,
}



export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
