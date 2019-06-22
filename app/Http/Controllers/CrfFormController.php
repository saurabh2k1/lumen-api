<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\Visit;
use App\Form;
use App\CrfForm;
use App\CrfFieldOption;
use Carbon\Carbon;

class CrfFormController extends Controller
{
    use ValidationTrait;

    public function new(Request $request, $id)
    {
        $forms = Form::where('_id', $id)->first();
        try {
        $existsFields = CrfForm::where('form_id', $forms->id)->get();
        foreach ($existsFields as $e) {
            CrfFieldOption::where('crf_form_id', $e->id)->delete();
            $e->delete();
        }
        $fields = $request['fields'];
        DB::statement('DROP TABLE IF EXISTS crf_form_' . $forms->id);

        $rawQuery = "Create TABLE crf_form_" . $forms->id . " (
            `id` int(11) NOT NULL AUTO_INCREMENT, 
         `site_id` int(11) NOT NULL,
        `subject_id` int(11) NOT NULL,
        `visit_id` int(11) NOT NULL,
        `dov` date DEFAULT NULL,";
        foreach ($fields as $field){
            if ($field['field_type'] === 'Date'){
                $rawQuery .= "`".$field['field_code']."` date DEFAULT NULL, ";
            } else {
                $rawQuery .= "`".$field['field_code']."` varchar(255) DEFAULT NULL, ";
            }
        }
        $rawQuery .= "
        `created_by` int(11) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `created_on` varchar(255) DEFAULT NULL,
        `isUpdated` tinyint(3) UNSIGNED DEFAULT NULL,
        `hasQuestion` tinyint(3) UNSIGNED DEFAULT NULL,
        PRIMARY KEY (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        DB::statement($rawQuery);
        DB::statement('ALTER TABLE crf_form_'. $forms->id .' 
            ADD UNIQUE KEY `unq_visit` (`site_id`,`subject_id`,`visit_id`);');
            $field_id = 1;
            foreach ($fields as $field) {
                $newForm = CrfForm::create([
                    'form_id' => $forms->id,
                    'field_id' => $field_id,
                    'field_code' => $field['field_code'],
                    'field_title' => $field['field_title'],
                    'field_type' => $field['field_type'],
                    'field_value' => $field['field_value'],
                    'field_required' => $field['field_required'],
                    'hasOption' => $field['hasOption'],
                    'ngShow_field' => $field['ngShow_field'],
                    'ngShow_value' => $field['ngShow_value'],
                    'min' => $field['min'],
                    'max' => $field['max'],
                    'regex' => $field['regex'],
                    'field_unit' => $field['field_unit'],
                ]);
                if ($field['hasOption']) {
                    $option_id = 1;
                    foreach ($field['options'] as $option) {
                        $newOption = CrfFieldOption::create([
                            'crf_form_id' => $newForm->id,
                            'option_id' => $option_id,
                            'option_title' => $option['option_title'],
                            'option_value' => $option['option_value']
                        ]);
                        $option_id++;
                    }
                }
                $field_id++;
            }
            return response()->json(['message' => 'Success' ], 201);
        } catch (\Exception $e) {
            //dd($e);
            return response()->json(['error' => 'Form Creation Failed'. $e], 403);
        }
    }

    public static function getOptionValue($formId, $fieldCode, $value)
    {
        $crfField = CrfForm::where('form_id', $formId)->where('field_code', $fieldCode)->first();
        if ($crfField->hasOption) {
            return CrfFieldOption::where('crf_form_id', $crfField->id)->where('option_value', $value)->value('option_title');
        }
        return $value;
    }

    
}