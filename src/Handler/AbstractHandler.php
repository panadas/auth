<?php
namespace Panadas\AuthManager\Handler;

use Panadas\AuthManager\User\UserInterface;
use Panadas\Util\String;

abstract class AbstractHandler implements HandlerInterface
{

    public function createToken(UserInterface $user)
    {
        return String::random(40);
    }
}
