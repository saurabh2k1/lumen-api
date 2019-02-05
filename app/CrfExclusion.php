<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

class CrfExclusion extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['id', 'study_id', 'site_id', 'patient_id',
        'dov', 'exclusion', 'reason', 'isUpdated', 'hasQuestion', 'created_by'];
    protected $dates = ['dov', 'created_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function study()
    {
        return $this->belongsTo('App\Study');
    }

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }
}