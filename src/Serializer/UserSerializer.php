<?php

namespace App\Serializer;

use App\Entity\User;

final class UserSerializer extends Serializer
{
    public function list(array $users): array
    {
        return array_map(function ($user) {
            return $this->details($user);
        }, $users);
    }

    public function details(User $user, ?bool $token = false): array
    {
        return [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles(),
        ];
    }
}