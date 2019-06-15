<?php
namespace App\Http\Controllers;


use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Visit;
use App\PatientVisit;

class PatientVisitController extends Controller
{
    use ValidationTrait;

    public function addVisit(Request $request)
    {
        $input = $request->all();
        $patient = Patient::where('_id', $input['patient_id'])->first();
        $visit = Visit::where('_id', $input['visit_id'])->first();
        $user = Auth::user(); 
        try {
            $newPVisit = PatientVisit::updateOrInsert([
                'patient_id' => $patient->id,
                'visit_id'   => $visit->id],
                [
                'visit_date' => $input['visit_date'],
                'isSkipped'  => $input['isSkipped'],
                'comments'   => $input['comment'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            return response()->json(['msg' => 'Submitted successfully'], 201);
        } catch (\Exception $e) {
            //dd($e);
            return response()->json(['error' => 'Visit Failed'. dd($e)], 403);
        }
    }

    public function getVisit($patientID, $visitID)
    {
        $patient = Patient::where('_id', $patientID)->first();
        $visit = Visit::where('_id', $visitID)->first();
        $pv = PatientVisit::where('patient_id', $patient->id)->where('visit_id', $visit->id)->first();
        return response()->json([$pv], 201);
    }
}