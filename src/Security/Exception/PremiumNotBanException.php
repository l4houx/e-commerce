<?php

namespace App\Security\Exception;

class PremiumNotBanException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Impossible de bannir un utilisateur premium', 0, null);
    }
}
