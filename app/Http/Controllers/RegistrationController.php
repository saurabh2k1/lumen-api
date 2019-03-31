<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccountMessage;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Site;
use Webpatser\Uuid\Uuid;

class RegistrationController extends BaseController
{
    private $requiredFields = [
        // 'username',
        'password',
        'first_name',
        'last_name',
        'email'
    ];

    public function registerEmail(Request $request)
    {
        $details = $request->only(
            // 'username',
            'password',
            'first_name',
            'last_name',
            'email',
            'site_id',
            'role_id'
        );

        $details['site_id'] = Site::where('_id', $details['site_id'])->value('id');
        
        $this->validate($request, [
            // 'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|distinct|unique:users',
        ]);
 
        $this->checkHasMinimum($details);

        try {
            $newUserId = $this->createUser($details);
            return response()->json(['_id' => $newUserId], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Registration Failed'], 403);
        }
    }

    private function checkHasMinimum($details)
    {
        foreach ($this->requiredFields as $field) {
            if (is_null($details[$field])) {
                throw new \Exception('There was a problem validating the requested registration.');
            }
        }
    }

    private function createUser($details)
    {
        $user = Auth::user();
       
        // DB::beginTransaction();

        $newUser = User::create([
            '_id' => Uuid::generate(4),
            'api_key' => Uuid::generate(4),
            'username' => $details['first_name'] . $details['last_name'],
            //$details['username'],
            'password' => Hash::make($details['password']),
            'first_name' => $details['first_name'],
            'last_name' => $details['last_name'],
            'email' => $details['email'],
            'site_id' => $details['site_id'],
            'role_id' => $details['role_id'],
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'confirm_code' => Uuid::generate(4)
        ]);

       // $this->addRole(Role::where('name', 'user')->first()->_id, $newUser);
        
        // Mail::to($details['email'])->send(new ConfirmAccountMessage($newUser));

        // DB::commit();

        return $newUser->_id->string;
    }

    public function confirmEmail(Request $request, $token)
    {
        try {
            $user = User::where('confirm_code', $token)->first();
            $user->confirmed_at = date("Y-m-d H:i:s");
            $user->save();
            // TODO this should render a response
            return response()->json(['result' => 'OK'], 200);
        } catch (\Exception $e) {
            throw new \Exception('There was a problem with the code.');
        }
    }

    //Assigning a role to the newly created user
    public function addRole($roleId, $newUser)
    {
        try {
            $role = Role::where('_id', $roleId)->first();
            $newUser->roles()->syncWithoutDetaching(
                [
                    $role->id =>
                        [
                            'created_by' => $newUser->id,
                            'updated_by' => $newUser->id,
                        ]
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception('There was a problem assigning the role.');
        }
    }
}
