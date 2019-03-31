<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

class PatientAudit extends BaseModel
{
    protected $fillable = ['id', 'patient_id', 'field', 'old_value', 'new_value', 'updated_by'];

    public function patient() {
        return $this->blongsTo('App\Patient');
    }
}