<?php

namespace App\Http\Controllers;

use App\Site;
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
     * Get all sites in the DB
     * 
     */
    public function getSites()
    {
        return response()->json(Site::all());
    }


    public function getSiteStudies($siteId){
        $site = Site::where('id', $siteId)->first();
        return response()->json($site->studies()->get());
    }

    public function getSiteWithUsers()
    {
        return response()->json(Site::with('users', 'studies')->get());
    }

    public function getMySite()
    {
        $siteId = Auth::user()->site()->get(['id', 'name']);
        //$siteDetails = Site::where('id', $siteId)->get();
        return response()->json($siteId);
    }
}

?>
