<?php

namespace App\Console\Commands;

use App\Services\DetteServiceImp;
use Illuminate\Console\Command;

class SendSmsDebts extends Command
{
    // Le nom et la description de la commande
    protected $signature = 'send:sms-debts';
    protected $description = 'Envoie des SMS aux clients avec des dettes chaque vendredi à 14h';

    protected $detteService;

    public function __construct(DetteServiceImp $detteService)
    {
        parent::__construct();
        $this->detteService = $detteService;
    }

    public function handle()
    {
        // Appeler le service pour envoyer les SMS
        $this->detteService->sendSmsToClientsWithDebts();
        $this->info('Les SMS ont été envoyés aux clients avec des dettes.');
    }
}
