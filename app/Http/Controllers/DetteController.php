<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetteRequest;
use App\Http\Requests\PaiementRequest;
use App\Models\Article;
use App\Models\Dette;
use App\Models\Paiement;
use App\Services\DetteServiceImp;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetteController extends Controller
{

    protected $detteService;

    public function __construct(DetteServiceImp $detteServiceImp)
    {
        $this->detteService = $detteServiceImp;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statut = $request->query('statut');
        $dettes = $this->detteService->getAllFiltered($statut);

        return response()->json([
            'status' => 200,
            'data' => $dettes,
            'message' => 'Liste des dettes'
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(DetteRequest $request)
    {
        $validatedData = $request->validated();
        $dette = $this->detteService->create($validatedData);
        return response()->json($dette, 201);
    }
    

    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $dette = $this->detteService->getById($id);
            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Dette trouvée'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Dette non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur est survenue lors de la récupération de la dette'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    public function getPaiements(string $id)
    {
        try {
            $dette = $this->detteService->getPaiements($id);
            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Paiements de la dette récupérés avec succès'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur est survenue lors de la récupération des paiements'
            ], 500);
        }
    }



    public function addPaiement(PaiementRequest $request, string $id)
    {
        try {
            $paiementData = $request->validated();
            $result = $this->detteService->addPaiement($id, $paiementData);
            
            $message = $result['detteComplete'] 
                ? 'Paiement ajouté avec succès. La dette est maintenant entièrement payée.'
                : 'Paiement ajouté avec succès.';

            return response()->json([
                'status' => 201,
                'data' => $result['dette'],
                'paiementEffectue' => $result['paiementEffectue'],
                'detteComplete' => $result['detteComplete'],
                'message' => $message
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getArticles(string $id)
    {
        try {
            $dette = $this->detteService->getArticles($id);
            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Articles de la dette récupérés avec succès'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur est survenue lors de la récupération des articles'
            ], 500);
        }
    }

    public function sendSmsToClientsWithDebts()
{
    try {
        $result = $this->detteService->sendSmsToClientsWithDebts();
        return response()->json([
            'status' => 200,
            'message' => $result
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Une erreur est survenue lors de l\'envoi des SMS : ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
