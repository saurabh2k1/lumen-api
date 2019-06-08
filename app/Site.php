<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['_id', 'name', 'code', 'department', 'contact_person', 'address'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function studies()
    {
        return $this->belongsToMany('App\Study');
    }

    public function patients()
    {
        return $this->hasMany('App\Patient');
    }

    public function aerecords()
    {
        return $this->hasMany('App\Aerecord');
    }
    

}
