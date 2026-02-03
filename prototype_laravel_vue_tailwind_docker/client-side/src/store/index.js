import { createStore } from 'vuex'
import auth from './modules/auth'
import user from './modules/user'
import ui from './modules/ui'
import server from './modules/server'
import notifications from './modules/notifications'

const store = createStore({
  modules: {
    auth,
    user,
    ui,
    server,
    notifications,
  },
})

export default store
