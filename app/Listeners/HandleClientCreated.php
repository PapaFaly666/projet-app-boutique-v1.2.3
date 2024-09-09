<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ClientQRCodeMail;
use Exception;
use Illuminate\Support\Facades\Log;

class HandleClientCreated
{
    protected $cloudinaryService;
    protected $maxRetries = 3;
    protected $retryDelay = 5; 

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function handle(ClientCreated $event)
    {
        $client = $event->client;
        $user = $event->user;
        $imageFile = $event->imageFile;

        // Upload de l'image avec tentatives de relance
        if ($imageFile && $imageFile->isValid()) {
            $uploadedImage = $this->uploadImageWithRetry($imageFile);
            if ($uploadedImage) {
                $user->image_url = $uploadedImage;
                $user->save();
            }
        }

        // Générer et envoyer le QR code par e-mail
        try {
            $qrCode = QrCode::format('png')->size(200)->generate($client->telephone);
            $qrCodeBase64 = base64_encode($qrCode);

            Mail::to($user->email)->send(new ClientQRCodeMail($user, $qrCodeBase64));
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du QR code par e-mail : ' . $e->getMessage());
        }
    }

    protected function uploadImageWithRetry($imageFile)
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            try {
                return $this->cloudinaryService->uploadImage($imageFile);
            } catch (Exception $e) {
                $lastException = $e;
                $attempts++;
                Log::warning("Tentative d'upload d'image échouée (tentative $attempts) : " . $e->getMessage());
                
                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }
        }

        Log::error("Échec de l'upload d'image après {$this->maxRetries} tentatives : " . $lastException->getMessage());
        return null;
    }
}