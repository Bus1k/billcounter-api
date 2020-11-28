<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'      => 'required|string|min:5|max:50',
            'email'     => 'required|string|email',
            'password'  => 'required|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response($validator->errors(), 400);
        }

        User::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password']),
        ]);

        return response(['message' => 'User created successfully.']);
    }

    /**
     * Display the specified resource.
     * @param User $user
     * @return User
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(Auth::id() !== $user['id'])
        {
            return response(['message' => 'Not Found'], 404);
        }

        $rules = [
            'password'  => 'required|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response($validator->errors(), 400);
        }

        $user->password = Hash::make($request['password']);
        $user->save();

        return response(['message' => 'Password changed successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if($user && Auth::id() === (int)$id)
        {
            $user->delete();
            return response(['message' => 'User deleted successfully.']);
        }

        return response(['message' => 'User not found.'], 404);
    }
}
