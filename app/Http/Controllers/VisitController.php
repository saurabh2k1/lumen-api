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
        return response()->json($visits);
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