<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Form extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['_id', 'study_id', 'name', 'code'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['id', 'study_id'];

    public function study()
    {
        return $this->belongsTo('App\Study');
    }

    public function visits()
    {
        return $this->belongsToMany(Visit::class);
    }

    public function fields()
    {
        return $this->hasMany(CrfForm::class);
    }

    public function field_options()
    {
        return $this->hasManyThrough('App\CrfFieldOption', 'App\CrfForm');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->_id = (string) Uuid::generate(4);
        });
    }
    
}
