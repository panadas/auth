<?php
namespace Panadas\AuthModule\User;

interface UserInterface
{

    public function getId();

    public function getTokens();
}
