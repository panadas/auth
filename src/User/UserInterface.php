<?php
namespace Panadas\AuthManager\User;

interface UserInterface
{

    public function getId();

    public function getTokens();
}
