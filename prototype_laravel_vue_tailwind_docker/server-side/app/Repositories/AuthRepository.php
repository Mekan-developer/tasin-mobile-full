<?php

namespace App\Repositories;

use App\Http\Resources\User\IndexResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(String $email): User
    {
        return User::where('email', $email)->first();
    }

    public function getAuthUserWithRole()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return new IndexResource($user);
    }

    public function getAuthUser(): User
    {
        $user = Auth::user();
        return $user;
    }
}
