<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Medical extends BaseModel 
{
    
    protected $fillable = [
        'medicalhistory_id', 'drugName', 'induction', 'eye', 'route', 'dose',  
        'startDate', 'endDate', 'isongoing', 
    ];

    protected $dates = [
        'startDate', 'endDate'
    ];

    protected $hidden = ['id'];

    public function medicalhistory()
    {
        return $this->belongsTo('App\Medicalhistory');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
}
