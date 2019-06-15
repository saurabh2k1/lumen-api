<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Visit extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['_id', 'code', 'description', 'min_days', 'max_days', 'study_id', 'is_repeating',
        'is_active', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['id', 'study_id'];

    public function study()
    {
        return $this->belongsTo('App\Study');
    }

    public function forms()
    {
        return $this->belongsToMany(Form::class);
    }

    

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
}