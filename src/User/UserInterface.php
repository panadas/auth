<?php
namespace Panadas\Auth\User;

interface UserInterface
{

    public function getId();

    public function getTokens();
}
