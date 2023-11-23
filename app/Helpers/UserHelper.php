<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Client;
use App\Models\Store;
use App\Exceptions\InvalidUserRoleException;
class UserHelper
{
    public static function getUserForRole(string $id)
    {
        $role = User::where('id', $id)->firstOrFail()->role;

        if ($role === 'client') {
            return Client::where('user_id', $id)->firstOrFail();
        } else if ($role === 'store') {
            return Store::where('user_id', $id)->firstOrFail();
        }

        throw new InvalidUserRoleException;
    }
}
