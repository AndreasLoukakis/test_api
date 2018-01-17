<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    
    protected $fillable = ['imo', 'name', 'email'];

    public $timestamps = false;

    /*  
     *   Relation to Reports model, join on imo
     */
    public function reports() {
        return $this->hasMany('Report', 'imo', 'imo');
    }

}
