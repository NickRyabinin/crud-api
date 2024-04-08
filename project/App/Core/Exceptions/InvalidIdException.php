<?php

/**
 * Класс InvalidIdException - просто наследник от \Exception
 * с "говорящим" названием. Выбрасывается в случае не совпадения
 * ID, переданного пользователем, с допустимым.
 */

namespace App\Core\Exceptions;

class InvalidIdException extends \Exception
{
}
