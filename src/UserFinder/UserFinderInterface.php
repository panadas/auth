<?php
namespace Panadas\AuthModule\UserFinder;

interface UserFinderInterface
{

    public function findById($id);

    public function findByCredentials($username, $password);
}
