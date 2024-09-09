<?php

namespace App\Jobs;

use App\Services\CloudinaryService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UploadClientImageJob implements ShouldQueue
{
    public $tries = 3; // Nombre de tentatives
    public $backoff = 5; // Délai entre les tentatives (en secondes)

    protected $imageFile;
    protected $user;
    protected $cloudinaryService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageFile, $user)
    {
        $this->imageFile = $imageFile;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CloudinaryService $cloudinaryService)
    {
        try {
            // Upload de l'image
            $uploadedImage = $cloudinaryService->uploadImage($this->imageFile);

            // Sauvegarde de l'URL de l'image dans l'utilisateur
            if ($uploadedImage) {
                $this->user->image_url = $uploadedImage;
                $this->user->save(); 
            }
        } catch (Exception $e) {
            // En cas d'échec, Laravel gère les tentatives automatiques selon les paramètres définis
            Log::error("Échec de l'upload d'image : " . $e->getMessage());

            // Si on veut forcer la relance manuellement en cas d'échec, on peut lever une exception
            throw $e; // L'exception provoque la relance du job
        }
    }
}
