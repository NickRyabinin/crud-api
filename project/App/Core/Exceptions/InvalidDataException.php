<?php

/**
 * Класс InvalidDataException - просто наследник от \Exception
 * с "говорящим" названием. Выбрасывается в случае не совпадения
 * пользовательских данных с ожидаемыми.
 */

namespace App\Core\Exceptions;

class InvalidDataException extends \Exception
{
}
