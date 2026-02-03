import api from '../api/http'

export default class ProfileController {
  static async getProfile() {
    const { data } = await api.get('/profile')
    return data
  }
}
