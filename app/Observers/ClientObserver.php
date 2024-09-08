<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Events\ClientCreated;

class ClientObserver
{
    public function created(Client $client)
    {
        $data = request()->all();

        // Validation des données utilisateur
        $validator = Validator::make($data['users'], [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Créer l'utilisateur associé au client
        $user = new User();
        $user->email = $data['users']['email'];
        $user->password = Hash::make($data['users']['password']);
        $user->role = 'client';
        $user->nom = $data['users']['nom'];
        $user->prenom = $data['users']['prenom'];
        $user->client_id = $client->id;
        $user->save();

        // Mettre à jour l'ID utilisateur dans le client
        $client->user_id = $user->id;
        $client->save();

        // Déclencher l'événement
        event(new ClientCreated($client, $user, $data['users']['image'] ?? null));
    }
}