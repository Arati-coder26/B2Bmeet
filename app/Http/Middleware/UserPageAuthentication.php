<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

class UserPageAuthentication
{
    private $routeAuthArray = array();
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->routeAuthArray = config('global.routeAuthArray');
        $routeName = $request->route()->uri();
        

        $userType = "";
        if(session()->has('logindata') && $routeName!="update_profile"){
         $logindata = session()->get('logindata');;
         $userType = isset($logindata['user_info']['user_type']) ? ($logindata['user_info']['user_type']) : ("");
        }                

        if(isset($this->routeAuthArray[$routeName]))
        {
         $permissionForRouteAuth = $this->routeAuthArray[$routeName]['authid'];
         $permissionPageId = $this->routeAuthArray[$routeName]['pageid'];
         
            if(session()->has('logindata')){
            $logindata = session()->get('logindata');;
                  if(isset($logindata['input_user_name']))
                     {
                        if(checkAuthForPage($permissionForRouteAuth,$permissionPageId))
                        {
                            
                        }else{
                            $data['pagetitle'] = "Not Authorized. Please go back";
                            $data['pageId'] = $permissionPageId;
                            return redirect("/notauthorized/".$permissionPageId);
                        }

                        
                     }
             }
        }
        if($userType=="Participant" && $routeName!="update_profile" && $routeName!="citySave"){
            $logindata = session()->get('logindata');;
            $profileInfoCheck = DB::table('users')->where('userid',$logindata['input_user_name'])->get()->toArray();
            if(count($profileInfoCheck)==1){
                
                if($profileInfoCheck[0]->profile_completion_status == "Incomplete"){

                    return redirect("update_profile");
                }
            }
        }


        
        return $next($request);
    }
}
