<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteStudy extends Pivot
{
    use SoftDeletes;

    public $timestamps = true;
}