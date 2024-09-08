<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ClientQRCodeMail;
use Exception;

class HandleClientCreated
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function handle(ClientCreated $event)
    {
        $client = $event->client;
        $user = $event->user;
        $imageFile = $event->imageFile;

        // Upload de l'image
        if ($imageFile && $imageFile->isValid()) {
            $uploadedImage = $this->cloudinaryService->uploadImage($imageFile);
            $user->image_url = $uploadedImage;
            $user->save();
        }

        // Générer et envoyer le QR code par e-mail
        try {
            $qrCode = QrCode::format('png')->size(200)->generate($client->telephone);
            $qrCodeBase64 = base64_encode($qrCode);

            Mail::to($user->email)->send(new ClientQRCodeMail($user, $qrCodeBase64));
        } catch (Exception $e) {
            // Log l'erreur ou la gérer d'une autre manière appropriée
            report($e);
        }
    }
}