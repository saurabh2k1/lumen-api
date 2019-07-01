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
use App\PatientVisit;

class MonitorController extends Controller
{
    
    public function getSummery($siteID)
    {
        $site = Site::where('_id', $siteID)->first();
        $patients = Patient::where('site_id', $site->id)->get();
        $visits = Visit::where('study_id', $patients[0]->study_id)->get();
        $tempV = array();
        foreach ($visits as $v) {
            array_push($tempV, $v->description);
        }
        $response = array();
        foreach ($patients as $p) {
            $temp = array();
            $temp['patient_id'] = $this->preparePatID($p->prefix, $p->pat_id);
            $ex = DB::table('crf_exclusions')->where('patient_id', $p->id)->value('exclusion');
             $temp['status'] = ($ex == 1) ? 'Enrolled': 'Exclusion not met';
             foreach ($visits as $v) {
                 if ($ex == 1) {
                    
                    $temp[$v->description] = $this->getVisitStatus($p->id, $v->id);
                 }
                
            }
            array_push($response, $temp);
        }

        return response()->json(['patients' => $response, 'visits' => $visits], 201);
    }

    private function preparePatID($prefix, $patID)
    {
        return $prefix. '-' . str_pad($patID, 3, '0', STR_PAD_LEFT);
    }

    private function getVisitStatus($patID, $VisitID)
    {
        $dov = '';
        $status = 0;
        $dov = PatientVisit::where('patient_id', $patID)
            ->where('visit_id', $VisitID)->value('visit_date');
        return array('dov' => $dov, 'status' => $status);
    }
}