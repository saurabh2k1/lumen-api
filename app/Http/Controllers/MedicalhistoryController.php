<?php

namespace App\Http\Controllers;

use App\Site;
use App\Study;
use App\Patient;
use App\Medicalhistory;
use App\Genmedical;
use App\Opmedical;
use App\Medical;
use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class MedicalhistoryController extends Controller
{
    use ValidationTrait;

    public function getMedicalHistory($patient_id)
    {
        // $site = Site::where('_id', $input['site_id'])->first();
        // $study = Study::where('_id', $input['study_id'])->first();
        $patient = Patient::where('_id', $patient_id)->first();
        $history = Medicalhistory::where('patient_id', $patient->id)
        // ->where('study_id', $patient->study_id)->where('site_id', $patient->site_id)
        ->with('genmedical', 'opmedical', 'Medical')->first();
        return response()->json([$history ], 201);
    }

    public function save(Request $request)
    {
        $input = $request->all();
               
        $patient = Patient::where('_id', $input['pat_id'])->first();
        try {
            $newHistory = Medicalhistory::updateOrCreate(
                [
                    'study_id' => $patient->study_id,
                    'site_id'  => $patient->site_id,
                    'patient_id' => $patient->id
                ],
                [            
                'visit_date' => date('Y-m-d')
            ]);
            $gmh = Genmedical::where('medicalhistory_id', $newHistory->id)->delete();
            
            foreach($input['genMedHistory'] as $m) {
                $gm = new Genmedical;
                $gm->medicalhistory_id = $newHistory->id;
                $gm->indication = $m['indication'];
                $gm->diagnosisDate  = date('Y-m-d', strtotime($m['diagnosisDate']));
                $gm->isongoing     = $m['isongoing'];
                $gm->treatment     = $m['treatment'];
                $gm->description   = $m['description'];
                if (isset($m['endDate'])) {
                    $gm->endDate = date('Y-m-d', strtotime($m['endDate']));
                } else {
                    $gm->endDate = null;
                }
                $gm->save();    
            }
            $gmh = Opmedical::where('medicalhistory_id', $newHistory->id)->delete();
            foreach($input['opMedHistory'] as $m) {
                $gm = new Opmedical;
                $gm->medicalhistory_id = $newHistory->id;
                $gm->indication = $m['indication'];
                $gm->eye        = $m['eye'];
                $gm->startDate  = date('Y-m-d', strtotime($m['startDate']));
                $gm->isongoing     = $m['isongoing'];
                $gm->treatment     = $m['treatment'];
                $gm->description   = $m['description'];
                if (isset($m['endDate'])) {
                    $gm->endDate = date('Y-m-d', strtotime($m['endDate']));
                } else {
                    $gm->endDate = null;
                }
                $gm->save();    
            }

            $gmh = Medical::where('medicalhistory_id', $newHistory->id)->delete();
            foreach($input['medicationHistory'] as $m) {
                $gm = new Medical;
                $gm->medicalhistory_id = $newHistory->id;
                $gm->drugName   = $m['drugName'];
                $gm->indication = $m['indication'];
                $gm->eye        = $m['eye'];
                $gm->route      = $m['route'];
                $gm->dose       = $m['dose'];
                $gm->startDate  = date('Y-m-d', strtotime($m['startDate']));
                $gm->isongoing     = $m['isongoing'];
                if (isset($m['endDate'])) {
                    $gm->endDate = date('Y-m-d', strtotime($m['endDate']));
                } else {
                    $gm->endDate = null;
                }
                $gm->save();    
            }

           return response()->json([$newHistory->id], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'History Creation Failed'], 403);
        }
    }
}
