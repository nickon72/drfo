<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Robota extends Model
{
    public function personal_data_r()
    {
        return $this->hasOne(Personal_data::class,'drfo_new');
    }

    public function residence_data_r()
    {
        return $this->hasOne(Residence_data::class,'drfo_new');
    }

//    public static function get_robota($drfo)
//    {
//        $robota = Robota::find($drfo);
//        return $robota->edrpou_rob;
//
//
//    }
    public static function get_robota($drfo)
    {
        $robota = DB::table('robota')
            ->select(
                'edrpou_rob'
            )
            ->where('drfo_new', $drfo)
            ->get();

        return $robota;

    }
}
