<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

class CrfChange extends BaseModel
{
    protected $fillable = ['id', 'form_id', 'row_id', 'field_code', 'old_value', 'new_value',
     'created_by', 'created_on'];
    
    public function form()
    {
        return $this->belongsTo('App\Form');
    }
}