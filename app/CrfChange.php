<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
// use Webpatser\Uuid\Uuid;

use App\Http\Controllers\CrfFormController;

class CrfChange extends BaseModel
{
    protected $fillable = ['id', 'form_id', 'row_id', 'field_code', 'old_value', 'new_value',
     'visit_id', 'patient_id', 'reason', 'created_by', 'created_on'];
    
    public function form()
    {
        return $this->belongsTo('App\Form');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public static function getCChanges($form_id, $row_id, $field_code)
    {
        $changes = CrfChange::where('form_id', $form_id)->where('row_id', $row_id)->where('field_code', $field_code)->with('creator')->get();
        $newChanges = array();
        foreach ($changes as $change) {
           $temp = $change;
           $temp['old_value'] = CrfFormController::getOptionValue($change['form_id'], $change['field_code'], $change['old_value']);
           $temp['new_value'] = CrfFormController::getOptionValue($change['form_id'], $change['field_code'], $change['new_value']);
           array_push($newChanges, $temp);
        }
        return ($newChanges);
    }
 
}