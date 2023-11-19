<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientCoponUse extends Model
{
    use HasFactory;

    public function client() : HasOne
    {
        return $this->hasOne(Client::class);
    }
    public function copon() : HasOne
    {
        return $this->hasOne(Copon::class);
    }
}
