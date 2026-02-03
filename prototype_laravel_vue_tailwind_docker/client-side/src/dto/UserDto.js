export class UserDto {
  constructor({ id = null, name = '', email = '',role = '', created_at = null } = {}) {
    this.id = id
    this.name = name
    this.email = email
    this.role = role
    this.created_at = created_at
  }
}

export function mapUserToDto(payload) {
  if (!payload) return new UserDto()
  return new UserDto({
    id: payload.id,
    name: payload.name,
    email: payload.email,
    role: payload.role,
    created_at: payload.created_at,
  })
}
