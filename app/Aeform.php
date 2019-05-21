<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aeform extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['_id', 'study_id', 'site_id', 'patient_id',
        'isEventOccur', 'isEyeAffected', 'eventName', 'otherEventName',
        'eventOccurOn', 'severity', 'startDate', 'description',
        'eventCriteria', 'causalityIOL', 'causalitySurgical', 'isDeviceMalfunction',
        'deviceMalfunction', 'otherMalfunction', 'isongoing', 'endDate',
        'actionTaken', 'resolution', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];

    public function patient() {
        return $this->blongsTo('App\Patient');
    } 

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }

}
