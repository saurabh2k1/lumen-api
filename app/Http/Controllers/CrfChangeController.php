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
use App\PatientAudit;
use App\CrfChange;
use App\Form;
use Carbon\Carbon;


class CrfChangeController extends Controller
{
    use ValidationTrait;

    public function create(Request $request) {
        $user = Auth::user();
        $input = $request->all();
        $form_id = Form::where('_id', $input['form_id'])->value('id');
        $patient_id = Patient::where('_id', $input['patient_id'])->value('id');
        $row_id = $input['row_id'];
        $sql = "UPDATE crf_form_". $form_id . " SET `isUpdated` = 1, `" . $input['field_code'] . "` = '" . $input['new_value'] ;
        $sql .= "' WHERE id = " . $row_id; 
        DB::statement($sql);
        try {
            $newChange = CrfChange::create([
                'form_id' => $form_id,
                'row_id'  => $row_id,
                'field_code' => $input['field_code'],
                'old_value'  => $input['old_value'],
                'new_value'  => $input['new_value'],
                'visit_id'   => $input['visit_id'],
                'patient_id' => $input['patient_id'],
                'reason'     => $input['reason'],
                'created_by' => $user->id,
                'created_on' => $request->ip(),
            ]);
            return response()->json(['newChange' => $newChange], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'CRF change Failed'], 403);
        }
    }

    public function getChanges($form_id, $row_id)
    {
        return response()->json(CrfChange::where('form_id', $form_id)->where('row_id', $row_id)->get());
    }
}