<?php

namespace App\Repository;

use App\Models\Article;

class ArticleRepositoryImp implements ArticleRepository{
    public function getAll(){
        return Article::query();
    }

    public function getById(string $id){
        return Article::find($id);
    }

    public function create(array $data){
        return Article::create($data);
    }

    public function update(string $id, array $data): ?Article
    {
        $client = Article::find($id);

        if (!$client) {
            return null;
        }

        $client->update($data);
        return $client;
    }

    public function delete(string $id){
        return Article::destroy($id);
    }

}