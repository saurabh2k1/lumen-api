<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Patient extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'study_id', 'site_id', 'initials', 'dob', 'gender', 'race', 'icf', 'icf_date', 'status',
        'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at','prefix', 'pat_id',
    ];
    protected $dates = [
        'dob', 'icf_date', 'deleted_at', 'created_at', 'updated_at'
    ];

    protected $hidden = ['id'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function study()
    {
        return $this->belongsTo('App\Study');
    }

    public function exclusion()
    {
        return $this->hasOne('App\CrfExclusion');
    }
    public static function boot()
{
    parent::boot();
    self::creating(function ($model) {
        $model->_id = (string) Uuid::generate(4);
    });
}
}