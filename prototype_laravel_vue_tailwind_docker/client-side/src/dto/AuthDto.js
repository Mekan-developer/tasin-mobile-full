export class AuthDto {
  constructor({ token = '', user = null } = {}) {
    this.token = token
    this.user = user
  }
}
