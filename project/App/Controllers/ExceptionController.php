<?php

namespace App\Controllers;

use App\Views\View;

class ExceptionController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function handleException(\Exception $e): void
    {
        $responseCode = $e->getCode();
        $message = [
            'error' => "{$e->getMessage()}"
        ];
        $this->view->send($responseCode, $message);
    }
}
