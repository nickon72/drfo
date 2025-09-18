<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    protected $fillable = ['company_id','fio','position','phone','email'];

    public static function add($fields)
    {
        $reference = new static;
        $reference->fill($fields);
        $reference->save();

        return $reference;

    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->delete();
    }

    public function getCompanyTitle($id)
    {
        $company = Company::find($id);
        return $company->title;
    }

}
