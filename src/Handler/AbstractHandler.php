<?php
namespace Panadas\AuthModule\Handler;

use Panadas\AuthModule\User\UserInterface;
use Panadas\Util\String;

abstract class AbstractHandler implements HandlerInterface
{

    public function createToken(UserInterface $user)
    {
        return String::random(40);
    }
}
