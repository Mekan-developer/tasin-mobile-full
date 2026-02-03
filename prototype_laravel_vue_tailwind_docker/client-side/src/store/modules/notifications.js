const state = () => ({
    notification:{
        message: '',
        type: '',
        show: false,
    }
})

const mutations = {
    SET_NOTIFICATION(state, { message, type, show = true }) {
        state.notification = {
            message,
            type,
            show,
        }
    },
    CLEAR_NOTIFICATION(state) {
        state.notification = {
            message: '',
            type: '',
            show: false,
        }
    }
}

const actions = {
    notify({ commit }, { message, type }) {
        commit('SET_NOTIFICATION', { message, type })
        setTimeout(() => {
            commit('SET_NOTIFICATION', { message: '', type: '', show: false })
        }, 4000) // Hide after 4 seconds
    },
    cancelNotify({ commit}){
        commit('CLEAR_NOTIFICATION')
    }
}

const getters = {
    notification: (state) => state.notification,
    isNotificationVisible: (state) => state.notification.show,
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
