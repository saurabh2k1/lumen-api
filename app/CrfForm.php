<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

class CrfForm extends BaseModel
{
    protected $fillable = ['id', 'form_id', 'field_id', 'field_code', 'field_title', 'field_type',
     'field_value', 'field_required', 'field_disabled', 'isEditable', 'hasOption', 'ngShow_field',
     'ngShow_value', 'min', 'max', 'regex', 'field_unit'];
    
    public function form()
    {
        return $this->belongsTo('App\Form');
    }

    public function options()
    {
        return $this->hasMany('App\CrfFieldOption', 'crf_form_id', 'id');
    }
}