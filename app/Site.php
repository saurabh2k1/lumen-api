<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['_id', 'name', 'code', 'department', 'contact_person', 'address'];
    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->hasMany('App\User');
    }

}
