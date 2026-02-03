<?php

namespace App\DTOs\Auth;

class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;
    public int $role_id;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->role_id = (int) $data['role_id'];
    }
}
