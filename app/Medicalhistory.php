<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Medicalhistory extends BaseModel 
{
    protected $table = 'medicalhistory';
    
    protected $fillable = [
        'patient_id', 'visit_date' 
    ];

    protected $dates = ['visit_date'];

    protected $hidden = ['id', 'site_id', 'study_id', 'patient_id'];

    public function genmedical()
    {
        return $this->hasMany('App\Genmedical');
    }

    public function opmedical()
    {
        return $this->hasMany('App\Opmedical');
    }

    public function Medical()
    {
        return $this->hasMany('App\Medical');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
}
