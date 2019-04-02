<?php
namespace App\Http\Controllers;


use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Patient;
use App\Site;
use App\Study;
use App\PatientAudit;
use App\CrfChange;
use Carbon\Carbon;


class CrfChangeController extends Controller
{
    use ValidationTrait;

    public function create(Request $request) {
        $user = Auth::user();
        $input = $request->all();
        $form_id = $input['form_id'];
        $row_id = $input['row_id'];
        try {
            $newChange = CrfChange::create([

            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}