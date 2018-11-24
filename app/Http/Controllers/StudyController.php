<?php

namespace App\Http\Controllers;

use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Study;
// use App\SiteStudy;

class StudyController extends Controller
{
    use ValidationTrait;

    public function getStudies()
    {
        return response()->json(Study::with('sites')->get());
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

    public function assignSite($studyId, $siteId){
        Study::find($studyId)->assignSite($siteId);
        
    }
}