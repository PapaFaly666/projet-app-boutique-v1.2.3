<?php
namespace App\Services;

interface ArticleService{
    public function getAllArticle();
    public function getArticleById(string $id);
    public function createArticle(array $data);
    public function updateArticle(string $id, array $data);
    public function deleteArticle(string $id);

    public function updateStockArticle(array $data, string $id);

    public function getByLibelleArticle(array $data);
    public function addStockArticle(array $data);

}