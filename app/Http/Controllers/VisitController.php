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
use App\Medicalhistory;
use Carbon\Carbon;

class VisitController extends Controller
{
    use ValidationTrait;

    public function getAll()
    {
        return response()->json(Visit::with('study')->get());
    }

    public function getVisits($studyID)
    {
        $studyId = Study::where('_id', $studyID)->value('id');
        $visits = Visit::where('study_id', $studyId)->with('study')->get();
        return response()->json($visits);
    }

    public function getVisit($id)
    {
        return response()->json(Visit::where('_id', $id)->with('study')->get());
    }

    public function getVisitByPatient($id)
    {
        $patient = Patient::where('_id', $id)->first();
        $visits = Visit::where('study_id', $patient->study_id)->with(['forms'])->get();
        $newVisits = array();
        
        foreach ($visits as  $v) {
            $tempVisit = $v;
            $isDone = true;
            $started = false;
            $exclusionForm = DB::select('select count(id) as cnt , dov from crf_exclusions WHERE patient_id = ? AND visit_id = ? ', [$patient->id, $v['id']] );
            if ($exclusionForm[0]->cnt > 0) {
                $tempVisit['dov'] = $exclusionForm[0]->dov;
            }
            if ($v['code'] == 'V1') {
                $medical = Medicalhistory::where('patient_id', $patient->id)->first();
                if ($medical){

                    if ($medical->visit_date) {
                        $tempVisit['medicalHistory'] = true; 
                        $isDone = true;
                        $started = true;
                    } else {
                        $tempVisit['medicalHistory'] = false;
                        $isDone = false;
                    }
                }
            }
            
            $newForms = array();
            foreach ($v['forms'] as $f) {
                $newF = $f;
                $values = DB::select('select count(id) as cnt, dov from crf_form_' . $f['id'] . ' Where visit_id = ? AND subject_id = ?', [$v['id'], $patient->id]);
                if ($values[0]->cnt > 0) {
                    $newF['isDone'] = true;
                    $tempVisit['dov'] = $values[0]->dov;
                    $started = true;
                } else {
                    $newF['isDone'] = false;
                }
                $isDone = $isDone && $newF['isDone'];
                //$newF['values'] = $values;
                array_push($newForms, $newF);
            }
            $tempVisit['forms'] = $newForms;
            $tempVisit['isDone'] = $isDone;
            array_push($newVisits, $tempVisit);
        }
        return response()->json($newVisits);
    }

    public function new(Request $request)
    {
        $studyId = Study::where('_id', $request['study'])->value('id');
        $user = Auth::user();
        try {
            $newVisit = Visit::create([
                'study_id' => $studyId,
                
                'code' => $request['code'],
                'description' => $request['name'],
                'min_days' => $request['min_days'],
                'max_days' => $request['max_days'],
                'is_repeating' => $request['isRepeating'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            return response()->json(['id' => $newVisit->_id], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Visits Creation Failed'], 403);
        }

    }

    public function updateVisit(Request $request, $id)
    {
        $studyId = Study::where('_id', $request['study'])->value('id');
        $user = Auth::user();
        try {
            $visit = Visit::where('_id', $id)->update([
               
                'study_id' => $studyId,
                'code' => $request['code'],
                'description' => $request['name'],
                'min_days' => $request['min_days'],
                'max_days' => $request['max_days'],
                'is_repeating' => $request['isRepeating'],
                
                'updated_by' => $user->id,
            ]);
            return response()->json(['message' => 'Visit Update Successfuls'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Visit Update Failed'], 403);
        }
    }

}