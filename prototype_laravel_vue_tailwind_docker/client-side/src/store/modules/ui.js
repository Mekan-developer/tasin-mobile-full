const THEME_KEY = 'tasin-theme'

const state = () => ({
    dropdownOpen: false,
    collapseSidebar: false,
    loading: false,
    /** Тема: 'dark' | 'light' */
    theme: typeof localStorage !== 'undefined' ? (localStorage.getItem(THEME_KEY) || 'dark') : 'dark',
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
    SET_THEME(state, value) {
        state.theme = value
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem(THEME_KEY, value)
        }
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
    /** Переключает тему dark/light и обновляет DOM */
    toggleTheme({ commit, state }) {
        const next = state.theme === 'dark' ? 'light' : 'dark'
        commit('SET_THEME', next)
        document.documentElement.classList.toggle('dark', next === 'dark')
    },
    /** Инициализирует тему при загрузке приложения */
    initTheme({ state }) {
        document.documentElement.classList.toggle('dark', state.theme === 'dark')
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
    isDarkTheme: (state) => state.theme === 'dark',
}



export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
