<?php

namespace App\Http\Controllers;

use App\Site;
use App\Study;
use App\Patient;
use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class SiteController extends Controller
{
    use ValidationTrait;
    
    /**
     * Create new Site
     * 
     */
    public function createSite(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required|string|unique:sites',
            'code' => 'required',
            'address' => 'required',
        ]);
        try {
            $newSite = Site::create([
                '_id' => Uuid::generate(4),
                'name' => $request['name'],
                'code' => $request['code'],
                'department' => $request['department'],
                'contact_person' => $request['contact_person'],
                'address' => $request['address'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
                ]);
            return response()->json(['_id' => $newSite->_id->string], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Site Creation Failed'], 403);
        }
    }

    /**
     * Update Site
     * 
     * @param Request $request
     * @param string $siteID
     * @return StatusCode
     */
    public function updateSite(Request $request, $siteId)
    {
        $fields = $request->all();
        $site = Site::where('_id', $siteId)->first();
        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }
        try {
            $site->update([
                'name' => $fields['name'],
                'code' => $fields['code'],
                'department' => $fields['department'],
                'contact_person' => $fields['contact_person'],
                'address' => $fields['address'],
                'updated_by' => Auth::user()->id,
            ]);
            return response()->json(['msg' => 'Site details updated.'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Site Update Failed'], 403);
        }
    }

    /**
     * Get all sites in the DB
     * 
     */
    public function getSites()
    {
        return response()->json(Site::all());
    }

    public function getSite($siteId)
    {
        $site = Site::where('_id', $siteId)->first();
        return response()->json($site, 201);
    }

   public function getSiteStudy()
   {
       $siteId = Auth::user()->site()->value('id');
       $site = Site::find($siteId)->studies()->get();
       return response()->json($site);
   }

    public function getSiteWithUsers()
    {
        return response()->json(Site::with('users', 'studies')->get());
    }

    
    public function getMySite()
    {
        $user = Auth::user();
        return response()->json($user->site()->get(['_id', 'name']));
        
    }

    public function getDashboard($siteId, $studyId)
    {
        $siteId = Site::where('_id', $siteId)->value('id');
        $studyId = Study::where('_id', $studyId)->value('id');
        // $site = Site::where('_id', $id)->withCount('patients')->first();
        $patCount = Patient::where('site_id', $siteId)->where('study_id', $studyId)->count(); 
        $res = array("pat_count" => $patCount);
        return response()->json($res);
    }

    private function getUserSite()
    {
        $user = Auth::user();
        return $user->site()->value('id');
    }
}

?>
