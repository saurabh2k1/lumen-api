<?php

namespace App\Http\Controllers;

use App\User;
use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Site;

class UserController extends Controller
{
    use ValidationTrait;

    /**
     * Get a user by their UUID
     *
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserById( $userId)
    {
        if (!$userId) {
            throw new \Exception('There was a problem retrieving the user.');
        }

        $this->isValidUserID($userId);

        $user = User::where('_id', $userId)->with(['role', 'site'])->first();

        return response()->json($user);
    }

    /**
     * Returns model with current users UUID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSelf()
    {
        $userId = Auth::user()->_id;

        $user = User::where('_id', $userId)->with('roles:_id,name')->first();

        return response()->json($user);
    }

    /**
     * Returns all users
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        return User::with(['role','site'])->get();
    }


    /**
     * Update any user by admin
     */
    public function updateUser(Request $request, $userId) {
        $input = $request->all();
        $site = Site::where('_id', $input['site_id'])->first();
        $user = User::where('_id', $userId)->first();
        $user->first_name = $input['first_name'];
        $user->last_name = $input['last_name'];
        $user->email = $input['email'];
        $user->site_id = $site->id;
        $user->role_id = $input['role_id'];
        $user->updated_by = Auth::user()->id;
        $user->save();
        return response()->json(['msg' => 'User Details Updated'], 201);
    }

    /**
     * Update user in the system
     */
    public function updateUserByUUID(Request $request, $userId)
    {
        $this->validate($request, [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ]);

        if (Auth::user()->_id !== $userId) {
            throw new \Exception('Illegal attempt to adjust another users details. ' .
                'The suspicious action has been logged.');
        }

        $this->isValidUserID($userId);

        $fields = $request->only([
            'firstName',
            'lastName',
        ]);

        $user = Auth::user();

        if (!is_null($fields['firstName'])) {
            $user->first_name = $fields['firstName'];
        }

        if (!is_null($fields['lastName'])) {
            $user->last_name = $fields['lastName'];
        }

        $user->updated_by = Auth::user()->id;
        $user->save();

        return response('OK', 200);
    }

    /**
     * Change the users password
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Exception
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
            'newpassword' => 'required|string|different:password',
        ]);

        $fields = $request->input();

        if (!isset($fields['newpassword'])) {
            throw new \Exception('There was a problem changing the password.');
        }

        $user = User::where('email', $fields['email'])->first();

        if (!Hash::check($request['password'], $user->password)) {
            throw new \Exception('There was a problem changing the password.');
        }

        $user->password = Hash::make($request['newpassword']);
        $user->updated_by = Auth::user()->id;
        $user->save();

        return response('OK', 200);
    }


    /**
     * Rseet the password of a user
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function resetPassword(Request $request)
    {
        $fields = $request->only(['userId', 'password']);
        $user = User::where('_id', $fields['userId'])->first();
        $user->password = Hash::make($fields['password']);
        $user->updated_by = Auth::user()->id;
        $user->save();
        return response()->json(['msg' => 'User Password Updated'], 201);
    }

 
    /**
     * Get the roles of a user
     *
     * @param $userId
     * @return mixed
     */
    public function getUserRoles($userId)
    {
        $user = User::loadFromUuid($userId);
        return $user->roles;
    }

    public function getUserSite(){
        $user = Auth::user();
        return response()->json($user->site()->get(['id', 'name']));
    }

    
    /**
     * Assign a new role to a user
     *
     * @param $userId
     * @param $role
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function assignRole($roleId, $userId)
    {
        User::loadFromUuid($userId)->assignRole($roleId);
        return response('OK', 200);
    }

    /**
     * Revoke a role from a user
     *
     * @param $userId
     * @param $role
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function revokeRole($roleId, $userId)
    {
        User::loadFromUuid($userId)->revokeRole($roleId);
        return response('OK', 200);
    }
}
