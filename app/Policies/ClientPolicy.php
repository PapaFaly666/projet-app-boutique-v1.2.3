<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create clients.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Assurez-vous que le rôle de l'utilisateur est 'admin' ou 'boutiquier'
        return in_array($user->role, ['admin', 'boutiquier']);
    }

    public function view(User $user)
    {
        // Vérifie si l'utilisateur a le rôle 'admin' ou 'boutiquier'
        return in_array($user->role, ['admin', 'boutiquier']);
    }
}
