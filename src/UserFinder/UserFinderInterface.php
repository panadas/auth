<?php
namespace Panadas\Auth\UserFinder;

interface UserFinderInterface
{

    public function findById($id);

    public function findByCredentials($username, $password);
}
