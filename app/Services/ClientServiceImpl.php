<?php 
namespace App\Services;

use App\Exceptions\ClientNotFoundException;
use App\Exceptions\ClientRepositoryException;
use App\Exceptions\ClientServiceException;
use App\Facades\ClientRepositoryFacade as ClientRepository;
use App\Http\Resources\ClientResource;
use App\Mail\ClientQRCodeMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClientServiceImpl implements ClientService{
    public function getAllClient(){
        return ClientRepository::getAll();
    }

    public function getClientById($id){
        $client = ClientRepository::getById($id);
        if(!$client){
            throw new ClientRepositoryException();
        }
        return ClientRepository::getById($id);
    }
    
    public function createClient(array $data): Client
{
    return DB::transaction(function () use ($data) {
        
        
        return ClientRepository::create([
            'surnom' => $data['surnom'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
        ]);
    });
}


    

    public function updateClient(string $id,array $data){
        $client = ClientRepository::update($id, $data);
        if(!$client){
            throw new ClientRepositoryException();
        }
        return ClientRepository::update($id, $data);
    }

    public function deleteClient(string $id)
{
    $client = ClientRepository::getById($id);
    
    if (!$client) {
        throw new ClientRepositoryException("Client not found");
    }
    ClientRepository::delete($id);    
    return $client;
}



    public function findByTelephoneClient($telephone)
    {
        // Appeler le dépôt pour trouver le client par téléphone
        $client = ClientRepository::findByTelephone($telephone);

        if (!$client) {
            throw new ClientRepositoryException();
        }
        return $client;
    }

    public function listerDettes($id)
    {
        $client = Client::with('dettes')->find($id);

        if (!$client) {
            throw new ClientServiceException("Client non trouvé.", false, null);
        }

        if ($client->dettes->isEmpty()) {
            throw new ClientServiceException('Client trouvé, aucune dette.', true, null);
        }

        return [
            'client' => new ClientResource($client),
            'dettes' => $client->dettes,
        ];
    }

    public function afficherCompteUser($clientId)
    {
        // Récupérer le client
        $client = Client::find($clientId);
    
        if (!$client || !$client->user) {
            throw new ClientServiceException("Client non trouvé.", false, null);
        }
    
        // Vérifier si l'utilisateur associé au client existe
        if (!$client->user) {
            throw new ClientServiceException("Utilisateur non trouvé pour ce client.", false, null);
        }
    
        // Retourner les informations de l'utilisateur
        return $client->user;
    }
    

    
}