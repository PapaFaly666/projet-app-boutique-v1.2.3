<?php

namespace App\Exceptions;

use Exception;

class ArticleServiceExcepion extends Exception
{

    protected $success;
    protected $data;

    public function __construct($message = "Pas d'article disponible.", $success = false, $data = null)
    {
        parent::__construct($message);
        $this->success = $success;
        $this->data = $data;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'success' => $this->success,
            'data' => $this->data
        ], 404);
    }
}
