<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
use Carbon\Carbon;

Class FileController extends Controller
{
    public function saveFile(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()){
            $file = $request->file('file');
            $user = Auth::user();
            $patient = Patient::where('_id', $request->input('patient_id'))->first();
            $visitID = Visit::where('_id', $request->input('visit_id'))->value('id');
            $fileName = $patient->prefix . '-' . str_pad($patient->id, 3, "0", STR_PAD_LEFT) . '-v' . $visitID. '.'.$file->getClientOriginalExtension();
            // Storage::put($fileName, File::get($file));
            
            $file->move('app', $fileName);
            DB::table('fileupload')->updateOrInsert([
                'patient_id' => $patient->id,
                'visit_id'   => $visitID,
            ], [
                'file'       => $fileName,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'File uploaded Successfully', 'filePath' => $fileName]);
        }
    }

    public function getFile($patient_id, $visitID)
    {
        $patient = Patient::where('_id', $patient_id)->first();
        $visitID = Visit::where('_id', $visitID)->value('id');
        $details = DB::table('fileupload')->select('file', 'created_at')->where('patient_id', $patient->id)
                ->where('visit_id', $visitID)->first();
        return response()->json($details);
    }
}