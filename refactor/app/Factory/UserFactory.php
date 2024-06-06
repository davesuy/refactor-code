<?php

namespace DTApi\Factory;

use DTApi\Models\Customer;
use DTApi\Models\Translator;

class UserFactory
{
    public static function getUserObject(User $currentUser)
    {
        if ($currentUser && $currentUser->is('customer')) {
            return new Customer();
        } else {
            return new Translator();
        }
    }
}