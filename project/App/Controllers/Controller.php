<?php

namespace App\Controllers;

use App\Views\View;
use App\Core\Helper;

abstract class Controller
{
    protected $model;
    protected $view;
    protected $helper;

    public function __construct($model, View $view, Helper $helper)
    {
        $this->model = $model;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function read()
    {
        $id = $this->helper->getId();
        if ($id === '') {
            $message = $this->model->index();
            if ($message === []) {
                $responseCode = '404';
                $message = ['error' => 'No records'];
            } else {
                $responseCode = '200';
            }
        } elseif ($id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->model->show($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function invalidMethod()
    {
        $responseCode = '405';
        $message = ['error' => 'Method not allowed'];
        $this->view->send($responseCode, $message);
    }
}
