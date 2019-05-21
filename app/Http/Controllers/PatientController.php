<?php
namespace App\Http\Controllers;


use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\User;
use App\PatientAudit;
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
        // return response()->json(Patient::where('_id', $id)->with(['audits'])->first());
        $pat = Patient::where('_id', $id)->with(['audits'])->first();
        $newAudit = array();
        foreach($pat->audits as $audit) {
            $newA = $audit;
            $user = User::find($audit['updated_by']);
            $newA['changedBy'] = $user->first_name . " " . $user->last_name;
            array_push($newAudit, $newA);
        }
        $pat->audits = $newAudit;
        return response()->json($pat);
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
            'reason' => $user->reason,
            ]);
            return response()->json(['id' => $newPat->_id], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Patient Creation Failed'], 403);
        }
    }

    public function update(Request $request, $id)
    {
        
        $user = Auth::user();
        $updatedFields = array();
        $input = $request->all();
        $oldPat = Patient::where('_id', $id)->first();
        if (!$oldPat) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        foreach ($input as $key => $value) {
            $updatedFields[$key] = $value;
            $oldValue = $oldPat[$key];
            PatientAudit::create([
                'patient_id' => $oldPat->id,
                'field' => $key,
                'old_value' => $oldValue,
                'new_value' => $value,
                'updated_by' => $user->id,
            ]);
        }
        $updatedFields['updated_by'] = $user->id;
        $updatedFields['isUpdated'] = true;
        try {
            $newPat = Patient::where('_id', $id)->update($updatedFields);
            // TODO: update Audit Trail
            return response()->json(['msg' => 'Patient Details Updated'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Patient update Failed'], 403);
        }
        
    }

    private function getPrefix($studyID, $siteID)
    {
        $siteCode = Site::where('id', $siteID)->value('code');
        $studyCode = Study::where('id', $studyID)->value('code');
        // return addslashes($studyCode . '/'. $siteCode);
        return $siteCode;
    }

    private function getNextPatID($study_id, $site_id)
    {
        $patient = Patient::where('study_id', $study_id)->where('site_id', $site_id);
        $max = (int)$patient->max('pat_id');
        return ($max+1);
    }


} 