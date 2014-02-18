<?php
namespace Panadas\AuthManager\UserFinder;

interface UserFinderInterface
{

    public function findById($id);

    public function findByCredentials($username, $password);
}
