<?php 
namespace App\Services;

use App\Exceptions\ArticleServiceExcepion;
use App\Models\Article;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImp;
use Illuminate\Support\Facades\Validator;

class ArticleServiceImp implements ArticleService{

    protected $articleRepo;

    public function __construct(ArticleRepositoryImp $articleRepo){
        $this->articleRepo = $articleRepo;
    }
    
    public function getAllArticle($disponible = null)
    {
        $query = $this->articleRepo->getAll();

        if ($disponible !== null) {
            $query->disponible($disponible);  
        }

        $articles = $query->get();

        if ($articles->isEmpty()) {
            throw new ArticleServiceExcepion();
        } else {

            throw new ArticleServiceExcepion('Liste des articles', true, $articles);
        }
    }
    public function getArticleById(string $id)
    {
        $article = Article::find($id);
        if(!$article){
            throw new ArticleServiceExcepion('Article non trouvé');
        }
            $article = Article::findOrFail($id);
            return [
                'status' => 200,
                'message' => 'Article trouvé',
                'data' => $article
            ];
    }
    public function createArticle(array $data)
    {
        // Validation des données
        $validator = Validator::make($data, [
            'libelle' => 'required|string|max:100',
            'prixUnitaire' => 'required|integer',
            'qteStock' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ArticleServiceExcepion('Validation échouée : ' . implode(', ', $validator->errors()->all()), 422);
        }

        // Vérification de l'existence de l'article
        $existingArticle = Article::where('libelle', $data['libelle'])->first();

        if ($existingArticle) {
            throw new ArticleServiceExcepion('Le libellé existe déjà. Veuillez choisir un autre libellé.', 411);
        }

        // Création de l'article
        $article = Article::create($data);

        return [
            'status' => 200,
            'message' => 'Article ajouté avec succès',
            'data' => $article
        ];
    }
    public function updateArticle(string $id, array $data)
    {
        // Validation des données
        $validator = Validator::make($data, [
            'libelle' => 'sometimes|required|string|max:100',
            'prixUnitaire' => 'sometimes|required|integer',
            'qteStock' => 'sometimes|required|integer',
        ]);
            $article = Article::find($id);
            if(!$article){
                throw new ArticleServiceExcepion('Article not found');
            }
            $article->update($data);

            return [
                'status' => 200,
                'message' => 'Article modifié avec succès',
                'data' => $article
            ];  
    }

    public function deleteArticle(string $id)
    {
        
        $article = Article::find($id);
        if(!$article){
            throw new ArticleServiceExcepion('Not found');
        }
        $article->delete();
        return [
            'status' => 200,
            'message' => 'Article supprimé avec succès'
        ];
        
    }

    public function updateStockArticle(array $data, string $id)
    {
        // Validation des données
        $validator = Validator::make($data, [
            'qteStock' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new ArticleServiceExcepion('Validation échouée : ' . implode(', ', $validator->errors()->all()), 422);
        }

        // Trouver l'article
        $article = Article::find($id);
        if (!$article) {
            throw new ArticleServiceExcepion("Article not found", 404);
        }

        // Mettre à jour le stock
        $article->qteStock = $data['qteStock'];
        $article->save();

        return [
            'status' => 200,
            'message' => 'Stock mis à jour',
            'data' => $article
        ];
    }

    public function getByLibelleArticle(array $data)
    {
        // Valider les données
        $validator = Validator::make($data, [
            'libelle' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            throw new ArticleServiceExcepion('Validation échouée : ' . implode(', ', $validator->errors()->all()), false, null);
        }

        // Chercher l'article par libellé
        $libelle = $data['libelle'];
        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            throw new ArticleServiceExcepion('Article non trouvé', false, null);
        }

        return [
            'status' => 200,
            'message' => 'Article trouvé',
            'data' => $article
        ];
    }

    public function addStockArticle(array $data)
{
    $updatedArticle = [];
    $incorrectArticles = [];

    foreach ($data['articles'] as $article) {
        $articleModel = Article::find($article['id']);

        if ($articleModel) {
            $articleModel->qteStock += $article['qteStock'];
            $articleModel->save();
            $updatedArticle[] = new ArticleServiceExcepion($articleModel);
        } else {
            $incorrectArticles[] = $article;
        }
    }

    if (!empty($incorrectArticles)) {
        throw new ArticleServiceExcepion('Certains articles sont incorrects.', 400, $incorrectArticles);
    }

    return [
        'status' => 200,
        'message' => 'Stock ajouté avec succès',
        'data' => null
    ];
}



}