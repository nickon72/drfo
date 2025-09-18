<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    public function residence()
    {
        return $this->hasMany(Residence_data::class);
    }
}
