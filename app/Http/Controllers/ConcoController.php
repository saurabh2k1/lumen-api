<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\Visit;
use App\Form;
use App\User;
use App\CrfForm;
use App\CrfChange;
use App\Conco;
use Carbon\Carbon;

class ConcoController extends Controller
{
    public function new(Request $request)
    {
        $input = $request->input();
        $patientId = Patient::where('_id', $input['patient_id'])->value('id');
        $user = Auth::user();
        try {
            $newConco = Conco::create([
                'patient_id' => $patientId,
                'drugName' => $input['drugName'], 
                'indication' => $input['indication'], 
                'eye' => $input['eye'],
                'route' => $input['route'], 
                'dose' => $input['dose'], 
                'startDate' => date('Y-m-d', strtotime($input['startDate'])), 
                'endDate' => ($input['isongoing'] == '0')? date('Y-m-d', strtotime($input['endDate'])): null, 
                'isongoing' => $input['isongoing'], 
                'created_by' => $user->id,
            ]);
            return response()->json(['msg' => 'Form Entry Successfully'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Form Entry Failed'], 403);
        }

    }

    public function get($patientId)
    {
        $patientId = Patient::where('_id', $patientId)->value('id');
        return response()->json(Conco::where('patient_id', $patientId)->with('creator')->get(), 201);
    }
}
