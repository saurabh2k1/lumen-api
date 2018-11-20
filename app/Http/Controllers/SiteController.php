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
                'created_by' => 1,
                'updated_by' => 1,
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

    public function getSiteWithUsers()
    {
        return response()->json(Site::with('users')->get());
    }
}

?>
