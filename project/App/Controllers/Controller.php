<?php

/**
 * Абстрактный класс Controller - родительский класс для контроллеров сущностей.
 * Использует трейт MessagesHandlers, куда вынесены обработчики, формирующие
 * многочисленные коды ответа и сообщения для пользователя.
 *
 * @todo выделить в наследники минимум два абстрактных класса, от которых будут
 * наследоваться соответственно контроллеры связанных (nested) и одиночных (single)
 * сущностей. Это позволит вынести общие методы соответствующих наследников в
 * родительские классы и с лёгкостью вводить новые сущности и контроллеры для них.
 */

namespace App\Controllers;

abstract class Controller
{
    use MessagesHandlers;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function read(): void
    {
        $page = $this->helper->getPage();
        $id = $this->helper->getId();
        match ($id) {
            '' => $this->handleEmptyId(page: $page),
            false => $this->handleInvalidId(),
            default => $this->handleValidId($id)
        };
    }
}
