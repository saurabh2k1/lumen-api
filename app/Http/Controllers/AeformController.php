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
use App\Aeform;
use Carbon\Carbon;

class AeformController extends Controller
{
    use ValidationTrait;

    public function saveAE(Request $request)
    {
        $inputs = $request->all();
        // $siteId = Site::where('_id', $request['site_id'])->value('id');
        // $studyId = Study::where('_id', $request['study_id'])->value('id');
        $patient = Patient::where('_id', $inputs['pat_id'])->first();
        $siteId = $patient->site_id;
        $studyId = $patient->study_id;
        $patientId = $patient->id;
        $user = Auth::user();
        try {
            $newAE = Aeform::create([
                'study_id' => $studyId,
                'site_id' => $siteId,
                'patient_id' => $patientId,
                'isEventOccur' => $inputs['isEventOccur'],
                'isEyeAffected' => $inputs['isEyeAffected'],
                'eventName' => $inputs['eventName'],
                'otherEventName' => $inputs['otherEventName'],
                'eventOccurOn' => $inputs['eventOccurOn'],
                'severity' => $inputs['severity'], 
                'startDate' => $inputs['startDate'], 
                'description' => $inputs['description'],
                'eventCriteria' => $inputs['eventCriteria'], 
                'causalityIOL' => $inputs['causalityIOL'], 
                'causalitySurgical' => $inputs['causalitySurgical'], 
                'isDeviceMalfunction' => $inputs['isDeviceMalfunction'],
                'deviceMalfunction' => $inputs['deviceMalfunction'], 
                'otherMalfunction' => $inputs['otherMalfunction'], 
                'isongoing' => $inputs['isongoing' ], 
                'endDate' => $inputs['endDate'],
                'actionTaken' => $inputs['actionTaken'], 
                'resolution' => $inputs['resolution'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            return response()->json(['id' => $patient], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Adverse Event Creation Failed'], 403);
        }
    }

}
