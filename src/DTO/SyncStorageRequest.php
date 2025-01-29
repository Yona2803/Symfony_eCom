<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SyncStorageRequest
    {
        public ?array $cart = null;
        public ?array $wishList = null;
    }
    