<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteStudy extends Pivot
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'site_id',
        'study_id',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    
}