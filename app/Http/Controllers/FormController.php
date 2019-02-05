<?php

namespace App\Http\Controllers;


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
use Carbon\Carbon;

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

    public function get($id)
    {
        try {
            
            $forms = Form::where('_id', $id)->with(['study', 'visits'])->first();
           
           $forms['fields'] = CrfForm::where('form_id', $forms->id)->with(['options'])->get();
            return response()->json($forms);
            
        } catch (\Exception $e) {
            dd($e);
            return response()->json($e);
        }
        
    }

    public function getAllForms($studyId)
    {
        $studyId = Study::where('_id', $studyId)->value('id');
        $forms = Form::where('study_id', $studyId)->with(['study','visits'])->get();
        return response()->json($forms);
    }
}