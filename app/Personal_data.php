<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Personal_data extends Model
{
    public function residence_data_pd()
    {
        return $this->hasOne(Personal_data::class,'drfo_new');
    }

//    protected $table = 'personal_data';
//
//    public function residenceData()
//    {
//        return $this->hasMany(ResidenceData::class, 'drfo_new', 'drfo_new');
//    }

    public function robota_pd()
    {
        return $this->hasOne(Robota::class, 'drfo_new');
    }


    public static function getdrfo($drfo)
    {
//        $date = Carbon::createFromFormat('d/m/y', $date)->format('Y-m-d');
//        $exp = Carbon::createFromFormat('d/m/y', $exp)->format('Y-m-d');
        $drfo_new = DB::table('personal_data')->select('drfo_new','surname','first_name','patronymic','birth_date')
            //->whereBetween('date', [$date, $exp])
            ->where('drfo_new',$drfo)
           //->groupBy('filia_id','materials_id','mera_id')
            ->get();

        return $drfo_new;

    }


}

