<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

//use app\Helpers\Miscellaneous;

use App\User;

class Login extends Controller
{
    //
    function index(Request $req){

        if($req->input('email') && $req->input('password'))
        {
        
        $inputdata['loginCheck'] =$loginCheck = checkUserLogin($req->input('email'),$req->input('password'));
            
            if($loginCheck['validation']==false){
                return view("signup.login",$inputdata);
            }else{
                $totalTimeVal = intval(config('global.session_logout_time'));
                $logoutTime = date("Y-m-d H:i:s",time()+($totalTimeVal));
                $loginCheck['login_valid_upto'] = $logoutTime;
                $req->session()->put('logindata',$loginCheck);    
            } 
        }


    	if($req->session()->has('logindata')){

                                   $forwardurl = "/dashboard";
                                    if(session()->has('forwardurl')) {
                                      $forwardurl = session()->get('forwardurl');
                                      
                                        session()->forget('forwardurl');
                                        \Session::save();
                                    }
    		return redirect($forwardurl);
    		
    	}
    	return $req->session()->get('data');

    }
    function loginPage(Request $req){
        
    	if(session()->has('logindata')){
    		return redirect("/dashboard");
    	}else{
    		return view('signup.login');
    	}
    }
    function registerView(){

        if(session()->has('logindata')){
            return redirect("/dashboard");
        }else{
            return view('signup.register');
        }   
    }

    function register(Request $req)
    {
        $userReg = new User;

        $response = $userReg->createNewParticipant($req->input());
            return $response;
    }
    function confirmemail($verificationCode,$userid){

        $data['verificationCode'] = $verificationCode;
        $data['userid'] = $userid;

        return view("signup.emailverification",$data);
    }
    function recoverPassword(Request $req){
        $userReg = new User;
            $response = $userReg->recoverPasswordForEmail($req->input('email'));
            return $response;
    }

}
