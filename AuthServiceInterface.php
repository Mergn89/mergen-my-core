<?php

namespace Core;

interface AuthServiceInterface
{
    public function check();


    public function getCurrentUser();


    public function login(string $login, string $password);


}
