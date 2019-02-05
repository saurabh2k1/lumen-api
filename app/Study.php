<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Site;

class Study extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [ 'name', 'description', 'status', 'created_by', 'updated_by'];
    protected $hidden = ['id'];
    public function sites(){
        return $this->belongsToMany('App\Site', 'site_study')->using('App\SiteStudy')->withTimestamps();
    }

    public function patients()
    {
        return $this->hasMany('App\Patient');
    }

    public function visits()
    {
        return $this->hasMany('App\Visit');
    }

    public function hasSite($siteId) {
        foreach($this->sites()->get() as $studySite){
            if($studySite['id'] === $siteId){
                return true;
            }
        }
        return false;
    }

    public function assignSite($siteId){
        $site = Site::find($siteId);
        if(!empty($site) 
         && !$this->hasSite($siteId)
        ){
            $this->sites()->syncWithoutDetaching(
                [
                    $site->id =>
                    [
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                ]
            );
       }
    }

    public function revokeSite($siteId){
        $site = Site::where('id', $siteId);
        if($this->hasSite($siteId)){
            $this->sites()->detach(
                $site->id
            );
        }
    }
}