<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Opmedical extends BaseModel 
{
    
    protected $fillable = [
        'medicalhistory_id', 'induction', 'eye', 'startDate', 'endDate', 'isongoing', 'treatment', 
        'description' 
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
