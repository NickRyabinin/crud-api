<?php

/**
 * Класс InvalidTokenException - просто наследник от \Exception
 * с "говорящим" названием. Выбрасывается в случае не совпадения
 * token, переданного пользователем, с допустимым.
 */

namespace App\Core\Exceptions;

class InvalidTokenException extends \Exception
{
}
