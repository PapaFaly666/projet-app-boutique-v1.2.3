<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ClientQRCodeMail;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
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

        $cloudinaryService = new CloudinaryService();
        $uploadedImage = null;

        // Vérifier si l'image est présente dans les données
        if (isset($data['users']['image']) && $data['users']['image']->isValid()) {
            $uploadedImage = $cloudinaryService->uploadImage($data['users']['image']);
        }

        // Créer l'utilisateur associé au client
        $user = new User();
        $user->email = $data['users']['email'];
        $user->password = Hash::make($data['users']['password']);
        $user->role = 'client';
        $user->nom = $data['users']['nom'];
        $user->prenom = $data['users']['prenom'];
        $user->image_url = $uploadedImage;
        $user->client_id = $client->id;
        $user->save();

        // Mettre à jour l'ID utilisateur dans le client
        $client->user_id = $user->id;
        $client->save();

        // Générer et envoyer le QR code par e-mail
        try {
            $qrCode = QrCode::format('png')->size(200)->generate($client->telephone);
            $qrCodeBase64 = base64_encode($qrCode);

            Mail::to($user->email)->send(new ClientQRCodeMail($user, $qrCodeBase64));
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'envoi du QR code ou de l\'email : ' . $e->getMessage());
        }
    }
}
