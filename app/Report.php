<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'imo',
        'created_on',
        'conditionType',
        'meHours',
        'meCons',
        'auxHours',
        'auxCons',
        'observedDistance'
    ];

    public $timestamps = false;

    /*  
     *   Relation to Vessel model, connection binds to field imo
     */
    public function vessel() {
        return $this->belongsTo('\App\Vessel', 'imo', 'imo');
    }
}