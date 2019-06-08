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
use App\Aerecord;
use Carbon\Carbon;

class AerecordController extends Controller
{
    use ValidationTrait;

    public function getAll()
    {
        return response()->json(Aerecord::get(), 201);
    }

    public function getById($id)
    {
        return response()->json(Aerecord::find($id), 201);
    }
    public function getByPatient($patID)
    {
        $patient = Patient::where('_id', $patID)->first();
        if (!$patient) {
            return response()->json(['msg'=> 'Patient not Found'], 404);
        }
        $aeRecords = Aerecord::where('patient_id', $patient->id)->get();
        return response()->json($aeRecords, 201);
    }

    public function getBySite($siteID)
    {
        $site = Site::where('_id', $siteID)->first();
        if (!$site) {
            return response()->json(['msg'=> 'Site not Found'], 404);
        }
        $aeRecords = Aerecord::where('site_id', $site->id)->get();
        return response()->json($aeRecords, 201);
    }

    public function new(Request $request)
    {
        $ae = $request->all();
        $user = Auth::user();
        $patient = Patient::where('_id', $ae['pat_id'])->first();
        if (!$patient) {
            return response()->json(['msg'=> 'Patient not Found'], 404);
        }
        try {
            $newAE = Aerecord::create([
                'site_id' => $patient->site_id,
                'patient_id' => $patient->id,
                'VISDAT' => date('Y-m-d', strtotime($ae['VISDAT'])),
                'AETERM' => $ae['AETERM'],
                'eventName' => $ae['eventName'],
                'otherEventName' => isset($ae['otherEventName']) ? 'otherEventName' : null,
                'AESTDATE' => date('Y-m-d', strtotime($ae['AESTDATE'])),
                'AEONGO' => $ae['AEONGO'],
                'AEENDAT' => $ae['AEONGO'] ? null :  $ae['AEENDAT'],
                'AEOUT' => $ae['AEOUT'],
                'AESEV' => $ae['AESEV'],
                'AESER' => $ae['AESER'],
                'AEACNOTH' => $ae['AEACNOTH'],
                'AEREL' => $ae['AEREL'],
                'AEACN' => $ae['AEACN'],
                'AEDEVREL' => $ae['AEDEVREL'],
                'aeSeq' => $ae['aeSeq'],
                'SAECLASS' => isset($ae['SAECLASS']) ? $ae['SAECLASS'] : null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            return response()->json(['AE' => $newAE], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'AERecord Creation Failed'], 403);
        }
    }
    
}
