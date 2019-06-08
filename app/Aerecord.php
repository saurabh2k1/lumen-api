<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Aerecord extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'patient_id', 'VISDAT', 'AETERM', 'eventName', 'otherEventName', 'AESTDATE', 'AEONGO', 
        'AEENDAT', 'AEOUT', 'site_id', 
        'AESEV', 'AESER', 'AEACNOTH', 'AEREL', 'AEACN', 'AEDEVREL', 'aeSeq', 'SAECLASS', 
        'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at',
    ];
    protected $dates = [
        'VISDAT', 'AESTDATE', 'AEENDAT', 'deleted_at', 'created_at', 'updated_at'
    ];

    protected $hidden = ['id', 'site_id'];

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
    
}

