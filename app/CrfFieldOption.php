<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

class CrfFieldOption extends BaseModel
{
    protected $fillable = ['id', 'crf_form_id', 'option_id', 'option_title', 'option_value'];

    public function crf_form()
    {
        return $this->belongsTo('App/CrfForm');
    }
}