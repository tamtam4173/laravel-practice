<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['company_name','street_address','representative_name'];

   
    public static function forSelect()
    {
        return static::orderBy('company_name')->pluck('company_name', 'id');
    }
}
