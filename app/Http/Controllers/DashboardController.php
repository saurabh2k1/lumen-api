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
use App\Form;
use App\CrfForm;
use App\PatientVisit;
use App\CrfExclusion;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get the counts for Dashboard
     * 
     */
    public function crfCount($studyID, $siteID)
    {
        $study_id = Study::where('_id', $studyID)->value('id');
        $site_id = Site::where('_id', $siteID)->value('id');
        $patients = Patient::where('site_id', $site_id)->where('study_id', $study_id)->with('exclusion')->whereHas('exclusion', function ($query){
            $query->where('exclusion', '=', 1);
        })->get();
        $pendingCount = 0;
        $completedCount = 0;
        $skippedVisitCount = 0;
        $AECount = 0;
        $SAECount = 0;
        foreach ($patients as $p) {
            $visits = PatientVisit::where('patient_id', $p->id)->whereNotNull('visit_date')->select('visit_id')->get();
            $skippedVisit = PatientVisit::where('patient_id', $p->id)->where('isSkipped', 1)->count();
            $skippedVisitCount += $skippedVisit;
            $forms = DB::table('form_visit')->whereIn('visit_id', $visits)->get();
            $completedCount += 3;
            if (Medicalhistory::where('patient_id', $p->id)->exists()) {
                $completedCount++;
            } else {
                $pendingCount++;
            }
            foreach($visits as $v){
                if (DB::table('fileupload')->where('patient_id', $p->id)->where('visit_id', $v->visit_id)->exists()) {
                    $completedCount++;
                } else {
                    $pendingCount++;
                }
            }
            foreach ($forms as $form) {
                if(DB::table('crf_form_'.$form->form_id)->where('visit_id', $form->visit_id)->where('subject_id', $p->id)->doesntExist()){
                    $pendingCount++;
                } else {
                    $completedCount++;
                }
            }

            $AECount += Aerecord::where('patient_id', $p->id)->count();
            $SAECount += Aerecord::where('patient_id', $p->id)->where('AESER', 1)->count();

        }
        $response = array('pendingCount' => $pendingCount, 'completedCount' => $completedCount, 'skippedVisitCount' => $skippedVisitCount);
        $response['updated_at'] = date('d-M-Y H:i:s');
        $response['AECount'] = $AECount;
        $response['SAE'] = $SAECount;
        return response()->json($response);
    }

    public function getPatientStatus( $siteID, $studyID)
    {
        $study_id = Study::where('_id', $studyID)->value('id');
        $site_id = Site::where('_id', $siteID)->value('id');
        $patients = Patient::where('study_id', $study_id)->where('site_id', $site_id)->with('exclusion')->get();
        $response = array();
        foreach ($patients as $p) {
            $temp['pat_id'] = $p->pat_id;
            $temp['prefix'] = $p->prefix;
            $temp['initials'] = $p->initials;
            $temp['status'] = 'Enrolled';
            if ($p->exclusion->exclusion !== 1) {
                $temp['status'] = 'Screen Failure';
            }
            //test for withdrawn
            // test for lost to follow-up
            //test for completed
            \array_push($response, $temp);
        }
        return response()->json($response);
    }
}