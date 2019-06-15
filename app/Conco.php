<?php

namespace App;

use Webpatser\Uuid\Uuid;

class Conco extends BaseModel
{
    protected $fillable = [
        'patient_id', 'drugName', 'indication', 'eye',
        'route', 'dose', 'startDate', 'endDate', 'isongoing', 'created_by'
    ];

    protected $hidden = ['id', 'patient_id'];

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
}
