<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;

use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $email = $request->input('email');
        $password = $request->input('password');
//        Log::info('login', ['email' => $email, 'password' => $password]);
        $count = User::where('email', '=', $email)
            ->where('password', '=', $password)
            ->count();
        if ($count == 1) {
            $token = JWTToken::CreateToken($email);
            return redirect('dashboard')->with('token', $token)->cookie('token', $token, time() + 60 * 60 * 24);
        }
        else{
            return Redirect::back()->withErrors([
                'message' => 'Invalid email or password',
            ]);
        }

    }

    function logout(Request $request){
        return redirect('/')->cookie('token', '', -1);
    }
    function getUsers(Request $request){
        $users = User::oldest()->get();

        return Inertia::render('Users', [
            'users' => $users
        ]);
    }
   function addUser(Request $request)
   {
       $data = $request->validate([
           'fullName' => 'required|string|max:25|min:3',
           'userName' => 'required|string|max:15|min:3|unique:users,userName',
           'phone' => 'required|string|max:17|min:11|unique:users,phone',
           'email' => 'required|string|email|max:50|unique:users,email',
           'role' => 'required|string|max:15|min:3',
           'password' => 'required|string|min:6',
       ]);

       DB::beginTransaction();
       try {
           User::create($data);
           DB::commit();
           return redirect()->back();
       } catch (Exception $e) {
           DB::rollBack();
           return back()->withErrors(['error' => $e->getMessage()]);
       }
   }

    function updateUser(Request $request, $id)
    {

        $data = $request->validate([
            'fullName' => 'required|string|max:25|min:3',
            'userName' => 'required|string|max:15|min:3|unique:users,userName,' . $id,
            'phone' => 'required|string|max:17|min:11|unique:users,phone,' . $id,
            'email' => 'required|string|email|max:50|unique:users,email,' . $id,
            'role' => 'required|string|max:15|min:3',
            'password' => 'required|string|min:6',
        ]);


       DB::beginTransaction();
        try {
            User::where('id', $id)->update($data);
            DB::commit();
            return Redirect::route('getUsers')->with('success', 'User Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }

    function deleteUser(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  User::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "User not found or could not be deleted";
            }
            $data = ['message' => 'User Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('users')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }

}
