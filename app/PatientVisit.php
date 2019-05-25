<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends BaseModel
{
    protected $fillable = ['patient_id', 'visit_id', 'visit_date', 'isSkipped', 'comment', 'created_by', 'updated_by'];
    protected $table = 'patient_visit';
    // public function patient()
    // {
    //     return $this->belongsTo('App\Patient');
    // }

    // public function visit()
    // {
    //     return $this->belongsTo('App\Visit');
    // }
    
}

