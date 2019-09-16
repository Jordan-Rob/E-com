<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Country;
use Auth;
use Session; 


class UsersController extends Controller
{
     public function register(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            //check if user already exists
            $usersCount = User::where('email', $data['email'])->count();
            if($usersCount>0){
                return redirect()->back()->with('flash_message_error', 'User with that email already exists!');
            };
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
                Session::put('frontSession', $data['email']);
                return redirect('/cart');
            };

         }
         
     }

     public function userLoginRegister(){
        return view('users.login_register');
     }

     public function checkEmail(Request $request){
        $data = $request->all();
        //check if user already exists
        $usersCount = User::where('email', $data['email'])->count();
        if($usersCount>0){
           echo "false";die;
        }else{
            echo "true";die;
        }    
    }

    public function account(Request $request){
        $user_id = Auth::user()->id;
        $userDetails = User::find($user_id);
        $countries = Country::get();

        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $user = User::find($user_id);
            $user->name = $data['name'];
            $user->address = $data['address'];
            $user->city = $data['city'];
            $user->state = $data['state'];
            $user->country = $data['country'];
            $user->pincode = $data['pincode'];
            $user->mobile = $data['mobile'];
            $user->save();
            return redirect()->back()->with('flash_message_success', 'Acoount details have been updated!');
        };

        return view('users.account')->with(compact('countries', 'userDetails'));
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
                Session::put('frontSession', $data['email']);
                return redirect('/cart');
            }else{
                return redirect()->back()->with('flash_message_error','Invalid Username or Password!!');
            };

        }

    }

    public function logout(){
        Auth::logout();
        Session::forget('frontSession');
        return redirect('/');
    }
}
