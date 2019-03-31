<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\ValidationTrait;
use Illuminate\Http\Request;
use App\User;
use App\Patient;
use App\Site;
use App\Study;
use App\Visit;
use App\Form;
use App\CrfForm;
use Carbon\Carbon;
use App\CrfExclusion;

class SiteFormController extends BaseController
{

    use ValidationTrait;

    public function saveExclusion(Request $request)
    {
        try {
        $studyId = Study::where('_id', $request['study_id'])->value('id');
        // $visitId = Visit::where('_id', $request['visit_id'])->value('id');
        $patientId = Patient::where('_id', $request['patient_id'])->value('id');
        $siteId = Site::where('_id', $request['site_id'])->value('id');
        $user = Auth::user();
        
            $exclusion = CrfExclusion::updateOrCreate(
                ['patient_id' => $patientId],
                [
                    'site_id' => $siteId,
                    'study_id' => $studyId,
                    'dov' => date('Y-m-d', strtotime($request['dov'])),
                    'exclusion' => $request['exclusion'],
                    'reason' =>  $request['reason'],
                    'isUpdated' => $request['isUpdated'],
                    'created_by' => $user->id,
                    'visit_id' => 1,
                ]);
                return response()->json(['form' => $exclusion], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => $e], 403);
        }
    }

    public function getExclusion($id)
    {
        $patientId = Patient::where('_id', $id)->value('id');
        return response()->json(CrfExclusion::where('patient_id', $patientId)->first());
    }
    
}