<?php

namespace App\Http\Controllers;

use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Study;
use App\Site;
// use App\SiteStudy;

class StudyController extends Controller
{
    use ValidationTrait;

    public function getStudies()
    {
        return response()->json(Study::with('sites')->get());
    }
    public function getAllStudies()
    {
        return response()->json(Study::all());
    }

    public function getStudy($studyId)
    {
        $study = Study::where('_id', $studyId)->with('sites')->first();
        if (!$study)
        {
            return response()->json(['error' => 'Study not found'], 404);
        } 
        return response()->json(['study' => $study], 201);
    }

    public function createStudy(Request $request) {
        
        $this->validate($request, [
            'name' => 'required|string|unique:studies',
        ]);
            
        try {
            $newStudy = Study::create([
                
                'name' => $request['name'],
                'description' => $request['description'],
                'status' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            $sSites = $request->input('sites');
            if(isset($sSites) && !empty($sSites)){
                foreach ($sSites as $site) {
                    $this->assignSite($newStudy->id, $site['id']);
                }
            }
            return response()->json(['_id' => $newStudy->id], 201);
            
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Study Creation Failed'], 403);
        }
    }

    /**
     * Update Study
     * 
     * @param Request $request
     * @param string $studyId
     * @return Status
     */
    public function updateStudy(Request $request, $studyId)
    {
        $study = Study::where('_id', $studyId)->first();
        $existingSites = $study->sites; 
        if (!$study)
        {
            return response()->json(['error' => 'Study not found.'], 404);
        }
        $fields = $request->all();
        try {
            $study->update([
                'name' => $fields['name'],
                'description' => $fields['description'],
                'status' => 0,
                'updated_by' => Auth::user()->id,
            ]);
            foreach ($existingSites as $exsite) {
                $site = Site::where('_id', $exsite['_id'] )->first();
                $study->revokeSite($exsite->id);
            }
            foreach ($fields['sites'] as $site) {
                $site = Site::where('_id', $site['_id'] )->first();
                $study->assignSite($site->id);
            }
            return response()->json(['msg' => 'Study updated successfully'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Study Update Failed'], 403);
        }
    }

    public function assignSite($studyId, $siteId){
        Study::find($studyId)->assignSite($siteId);
        
    }
}