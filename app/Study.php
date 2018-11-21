<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Study extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function sites(){
        return $this->belongsToMany('App\Site', 'site_study')->using('App\SiteStudy')->withTimestamps();
    }
}