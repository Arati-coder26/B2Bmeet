<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class UserAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $logindata = session()->get('logindata');

        if(!session()->has('logindata')){
            if($request->has('ajaxcall')){
                $errors = array("Session Timed Out/ You are not logged in");
                    $response['validation'] = false;
                    $response['errors'] = $errors;
                    $response['message'] = "Sorry";
                    echo json_encode($response);exit();

            }else{
            return redirect("/login");
            }
        }
            
        $routeName = $request->route()->uri();
        

            $superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();


            $logindata = session()->get('logindata');

            if(count($superAdminCheck)==0 && ($routeName != 'superadminprofile') && (isset($logindata['input_user_name'])) && ($logindata['input_user_name']=='admin') )
            {
                return redirect("/superadminprofile");
            }

            // check session time...
            $logindata = session()->get('logindata');
            if(isset($logindata['login_valid_upto']))
            {
                $currentTime = strtotime(date("Y-m-d H:i:s"));
                $loginValidUpto = strtotime($logindata['login_valid_upto']);
                if($loginValidUpto < $currentTime){
                    session()->put('sessiontimedout',TRUE);
                    $forwardPath = \Request::path();    
                    session()->put('forwardurl',$forwardPath);    
                    session()->forget('logindata');
                    \Session::save();

                    if($request->has('ajaxcall')){
                        $errors = array("Session Timed Out/ You are not logged in");
                            $response['validation'] = false;
                            $response['errors'] = $errors;
                            $response['message'] = "Sorry";
                            echo json_encode($response);exit();

                    }
                }else{
                    $totalTimeVal = intval(config('global.session_logout_time'));
                    $logoutTime = date("Y-m-d H:i:s",time()+($totalTimeVal));
                    $logindata['login_valid_upto'] = $logoutTime;
                    session()->put('logindata',$logindata);  
                    \Session::save();
                }
                $logindata = session()->get('logindata');

            }else{
                session()->forget('logindata');
                \Session::save();
            }

            $logindata = session()->get('logindata');
            \Session::save();


        if(!session()->has('logindata')){
            return redirect("/login");
        }
            // if current time is less than next logout time extend the session time out by another n minutes

            // else set a flash session and redirect to login page by unsetting the session of user
        return $next($request);
    }
}
