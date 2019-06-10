<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\Visit;
use App\Form;
use App\User;
use App\CrfForm;
use App\CrfChange;
use Carbon\Carbon;
use Illuminate\Validation\Validator;


class FormController extends Controller
{
    use ValidationTrait;

    public function new(Request $request)
    {
        $studyId = Study::where('_id', $request['study'])->value('id');
        $user = Auth::user();
        try {
            $newForm = Form::create([
                'study_id' => $studyId,
                'name' => $request['name'],
                'code' => $request['code'],
                'created_by' => $user->id,
            ]);
            $visits = Visit::whereIn('_id', $request['visits'])->get();
            $newForm->visits()->attach($visits);
            return response()->json(['id' => $newForm->_id], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Form Creation Failed'], 403);
        }
    }

    /**
     * Ger CRF Form Details 
     *
     * @param string $id
     * @return Response
     */
    public function get($id)
    {
        $forms = Form::where('_id', $id)->with(['study', 'visits'])->first();
        if($forms){
            $forms['fields'] = CrfForm::where('form_id', $forms->id)->with(['options'])->get();
            return response()->json($forms);
        } else {
            return response()->json(['error' => 'Wrong parameters'], 404);
        }
    }

    /**
     * Update CRF Form details
     * 
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $forms = Form::where('_id', $id)->first();
        $data = $request->all();
        try
        {
            $forms->update([
                'name' => $data['name'],
                'code' => $data['code'],
            ]);
            $visits = $forms->visits()->get();
            $forms->visits()->detach($visits);
            $visits = Visit::whereIn('_id', $request['visits'])->get();
            $forms->visits()->attach($visits);
            return response()->json(['msg' => 'Form updated'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Form Update Failed'], 403);
        }
        
    }

    public function getForm($id)
    {
        $input = Input::only('site_id', 'subject_id', 'visit_id');
        $forms = Form::where('_id', $id)->first();
        $siteId =  Site::where('_id', $input['site_id'])->value('id');
        $subjectId = Patient::where('_id', $input['subject_id'])->value('id');
        $visitId = Visit::where('_id', $input['visit_id'])->value('id');
        if($forms){
            
            $formValue = DB::table('crf_form_' . $forms->id)->where('site_id', $siteId)->where('visit_id', $visitId)->where('subject_id', $subjectId)->first();
            if($formValue){
                $user = User::find($formValue->created_by );
                $formValue->created_by = $user->first_name . " " . $user->last_name;
                $forms['value'] = $formValue;
            }
            $fields = CrfForm::where('form_id', $forms->id)->with(['options'])->get();
            $formFields = array();
            foreach ($fields as $field) {
                $f = array();
                $f['field_id'] = $field['field_id'];
                $f['type'] = $field['field_type'];
                $f['label'] = $field['field_title'];
                $f['inputType'] ='text';
                $f['name'] = $field['field_code'];
                $f['value'] = $field['field_value'];
                $f['unit'] = $field['field_unit'];
                $f['hasOption'] = $field['hasOption'];
                $f['options'] = array();
                $f['ngShow_field'] = $field['ngShow_field'];
                $f['ngShow_value'] = $field['ngShow_value'];
                if($field['hasOption']){
                    foreach ($field['options'] as $o) {
                        array_push($f['options'], array('value' => $o['option_value'], 'title' => $o['option_title']));
                    }
                }
                $f['validations'] = array();
                if($field['field_required']){
                    $v = array();
                    $v['name'] = 'required';
                    $v['validator'] = 'Validators.required';
                    $v['message'] = $field['field_title'] . ' Required!';
                    array_push($f['validations'], $v);
                }
                if($field['regex']){
                    $v = array();
                    $v['name'] = 'pattern';
                    $v['value'] = $field['regex'];
                    $v['validator'] = "Validators.pattern";
                    $v['message'] = ' Wrong pattern!';
                    array_push($f['validations'], $v);
                }
                if($field['min']){
                    $v = array();
                    $v['name'] = 'min';
                    $v['value'] = $field['min'];
                    $v['validator'] = "Validators.min";
                    $v['message'] = ' Value should be more than or equal to ' . $field['min'] ;
                    array_push($f['validations'], $v);
                }
                if($field['max']){
                    $v = array();
                    $v['name'] = 'max';
                    $v['value'] = $field['max']; 
                    $v['validator'] = "Validators.max";
                    $v['message'] = ' Value should be less than or equal to ' . $field['max'] ;
                    array_push($f['validations'], $v);
                }
                
                if ($formValue){
                    if ($formValue->isUpdated) {
                        $f['changes'] = CrfChange::where('row_id', $formValue->id)->where('form_id', $forms->id)->where('field_code', $field['field_code'])->with('creator')->get();
                    }
                }
               

                array_push($formFields, $f);
            }
            $fbutton = array();
            $fbutton['type'] = 'button';
            $fbutton['label'] = 'Save';
            array_push($formFields, $fbutton);
            $forms['fields'] = $formFields;

            return response()->json($forms);
        }

    }

    public function saveCRF(Request $request)
    {
        // $studyId = Study::where('_id', $request['study_id'])->value('id');
        $siteId =  Site::where('_id', $request['site_id'])->value('id');
        $subjectId = Patient::where('_id', $request['subject_id'])->value('id');
        $visitId = Visit::where('_id', $request['visit_id'])->value('id');
        $formId = Form::where('_id', $request['form_id'])->value('id');
        $fields = CrfForm::where('form_id', $formId)->with(['options'])->get();
        $user = Auth::user();
        $valueArray = array($siteId, $subjectId, $visitId, date('Y-m-d', strtotime($request['dov'])));

        try { 
            $newFormSQL = "insert into crf_form_" . $formId . "( `site_id`, `subject_id`, `visit_id`, `dov` ";
            $qMark = "";
            foreach ($fields as $field) {
                $newFormSQL .= ", `".$field['field_code']."` ";
                array_push($valueArray, $request[$field['field_code']]);
                $qMark .= "?,";
            }
            $newFormSQL .= ", `created_by`, `created_on`) values (?,?,?,?,". $qMark. " ?,?)";
            array_push($valueArray, $user->id, $_SERVER['REMOTE_ADDR']);
            // if (DB::insert($newFormSQL, $valueArray)) {
                DB::insert($newFormSQL, $valueArray);
                return response()->json(['msg' => 'Form saved'], 201);
            // }
            // return response()->json(['msg' => $newFormSQL, 'values' => $valueArray], 201);
        } catch (\Exception $e) {
            //dd($e);
            return response()->json(['error' => 'Form Creation Failed' . $e], 403);
        }
        
    }


    public function getAllForms($studyId)
    {
        $studyId = Study::where('_id', $studyId)->value('id');
        $forms = Form::where('study_id', $studyId)->with(['study', 'visits'])->get();
        return response()->json($forms);
    }
}