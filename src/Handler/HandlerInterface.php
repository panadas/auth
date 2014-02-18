<?php
namespace Panadas\AuthModule\Handler;

use Panadas\AuthModule\User\UserInterface;

interface HandlerInterface
{

    public function createToken(UserInterface $user);

    public function create(UserInterface $user, $lifetime = null);

    public function retrieve($token);

    public function update($token, \DateTime $modified);

    public function delete($token);

    public function gc();
}
