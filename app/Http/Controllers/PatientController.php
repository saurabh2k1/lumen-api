<?php
namespace App\Http\Controllers;


use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use Carbon\Carbon;


class PatientController extends Controller
{
    use ValidationTrait;

    public function getAll() 
    {
        return response()->json(Patient::all());
    }

    public function delete($id)
    {
        //TODO: check for releated entry
        $patient = Patient::find($id);
        $patient->delete();
        return response('Patient Deleted');
    }

    public function getPatient($id)
    {
        return response()->json(Patient::where('_id', $id)->first());
    }

    public function getPatients($siteID, $studyID)
    {
        $siteId = Site::where('_id', $siteID)->value('id');
        $studyId = Study::where('_id', $studyID)->value('id');
        $patients = Patient::where('site_id', $siteId)->where('study_id', $studyId)->get();
        return response()->json($patients);
    }

    public function new(Request $request)
    {
        
        $siteId = Site::where('_id', $request['site_id'])->value('id');
        $studyId = Study::where('_id', $request['study_id'])->value('id');
        $user = Auth::user();
        try {
            $newPat = Patient::create([
            
            'study_id' => $studyId,
            'site_id' => $siteId,
            'initials' => $request['initials'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'race' => $request['race'],
            'icf' => $request['icf'],
            'icf_date' => $request['icf_date'],
            'status' => 0,
            'pat_id' => $this->getNextPatID($studyId, $siteId ),
            'prefix' => $this->getPrefix($studyId, $siteId ),
            'created_by' => $user->id,
            'updated_by' => $user->id,
            ]);
            return response()->json(['id' => $newPat->_id], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Patient Creation Failed'], 403);
        }
    }

    public function update(Request $request, $id)
    {
        // $siteId = Site::where('_id', $request['site_id'])->value('id');
        // $studyId = Study::where('_id', $request['study_id'])->value('id');
        $user = Auth::user();
        try {
            $newPat = Patient::where('_id', $id)->update([
            
            // 'study_id' => $studyId,
            // 'site_id' => $siteId,
            'initials' => $request['initials'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'race' => $request['race'],
            'icf' => $request['icf'],
            'icf_date' => $request['icf_date'],
            'status' => 0,
            'updated_by' => $user->id,
            ]);
            return response()->json(['msg' => 'updated'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Patient update Failed'], 403);
        }
    }

    private function getPrefix($studyID, $siteID)
    {
        $siteCode = Site::where('id', $siteID)->value('code');
        $studyCode = Study::where('id', $studyID)->value('code');
        return addslashes($studyCode . '/'. $siteCode);
    }

    private function getNextPatID($study_id, $site_id)
    {
        $patient = Patient::where('study_id', $study_id)->where('site_id', $site_id);
        $max = (int)$patient->max('pat_id');
        return ($max+1);
    }


} 