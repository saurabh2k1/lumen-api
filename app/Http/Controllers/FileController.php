<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\Visit;
use App\Form;
use App\User;

Class FileController extends Controller
{
    public function saveFile(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()){
            $file = $request->file('file');
            $patient = Patient::where('_id', $request->input('patient_id'))->first();
            $visitID = Visit::where('_id', $request->input('visit_id'))->value('id');
            $fileName = $patient->prefix . '-' . str_pad($patient->id, 3, "0", STR_PAD_LEFT) . '-v' . $visitID. '.'.$file->getClientOriginalExtension();
            // Storage::put($fileName, File::get($file));
            
            $file->move('app', $fileName);
            return response()->json('success');
        }
    }
}