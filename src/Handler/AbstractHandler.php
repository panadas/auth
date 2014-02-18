<?php
namespace Panadas\Auth\Handler;

use Panadas\Auth\User\UserInterface;
use Panadas\Util\String;

abstract class AbstractHandler implements HandlerInterface
{

    public function createToken(UserInterface $user)
    {
        return String::random(40);
    }
}
