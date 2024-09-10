<?php

namespace App\Repository;

use App\Models\Article;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DetteRepositoryImp implements DetteRepository{

    public function getAll(){
        Dette::all();
    }
    public function getById(string $id)
    {
        return Dette::with(['client', 'articles', 'paiements'])
            ->findOrFail($id);
    }
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dette = Dette::create([
                'client_id' => $data['clientId'],
                'montant' => $data['montant'],
                'date' => now(),
                'montantDu' => $data['montant'],
                'montantRestant' => $data['montant']
            ]);

            foreach ($data['articles'] as $articleData) {
                $article = Article::findOrFail($articleData['articleId']);
                $dette->articles()->attach($article->id, [
                    'quantite' => $articleData['qteVente'],
                    'prix' => $articleData['prixVente']
                ]);

                $article->decrement('qteStock', $articleData['qteVente']);
            }

            if (isset($data['paiement']) && $data['paiement']['montant'] > 0) {
                $paiement = new Paiement([
                    'montant' => $data['paiement']['montant'],
                    'date' => now()
                ]);
                $dette->paiements()->save($paiement);

                $dette->montantRestant -= $paiement['montant'];
                $dette->save();
            }

            return $dette;
        });
    }
    public function update(string $id, array $data){
        $dette = Dette::find($id);
        if($dette){
            $dette->update($data);
            return $dette;
        }
        return null;
    }
    public function delete(string $id){
        
    }

    public function getPaiements(string $detteId)
    {
        try {
            return Dette::with('paiements')->findOrFail($detteId);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Dette non trouvée.");
        }
    }

    public function addPaiement(string $detteId, array $paiementData)
    {
        return DB::transaction(function () use ($detteId, $paiementData) {
            $dette = Dette::findOrFail($detteId);
            
            if ($dette->montantRestant <= 0) {
                throw new \Exception('Cette dette a déjà été entièrement payée.');
            }

            if ($paiementData['montant'] > $dette->montantRestant) {
                throw new \Exception('Le montant du paiement est supérieur au montant restant de la dette.');
            }

            $paiement = new Paiement(['montant' => $paiementData['montant']]);
            $dette->paiements()->save($paiement);

            $dette->montantRestant -= $paiementData['montant'];
            $dette->save();

            $detteComplete = $dette->montantRestant == 0;

            if ($detteComplete) {
                // Vous pouvez ajouter ici une logique supplémentaire pour marquer la dette comme entièrement payée
                // Par exemple, si vous avez un champ 'statut' dans votre modèle Dette :
                // $dette->statut = 'payée';
                // $dette->save();
            }

            return [
                'dette' => $dette->load('paiements'),
                'paiementEffectue' => $paiementData['montant'],
                'detteComplete' => $detteComplete
            ];
        });
    }


    public function getArticles(string $detteId)
    {
        try {
            return Dette::with('articles')->findOrFail($detteId);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Dette non trouvée.");
        }
    }


    public function getAllFiltered($statut = null)
    {
        $query = Dette::with(['client']);

        if ($statut === 'Solde') {
            $query->where('montantRestant', 0);
        } elseif ($statut === 'NonSolde') {
            $query->where('montantRestant', '>', 0);
        }

        return $query->get();
    }


    
}