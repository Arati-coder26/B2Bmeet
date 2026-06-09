<?php

use Illuminate\Support\Facades\DB;



class LoginCheck{
	
	function checkLogin($userName,$password){
		
		$responseValidation = array();
		$responseUserInfo = array();
		$defaultCredentials = FALSE;
		$invalidEmail = false;
		// echo $userName;exit();
		$users = DB::table('users')->whereRaw(" (email = '".$userName."' OR userid='".$userName."') AND password='".(md5($password))."' ")->get()->toArray();

		// $users = DB::table('users')->whereRaw(" (email = '".$userName."' OR userid='".$userName."') ")->get()->toArray();
		
		// DB::table('users')->where('id', $users[0]->id)->update(['password' => md5($password)]);
		
		if(count($users)==0)
		{
			$superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();
			if(count($superAdminCheck)==0)
			{
				$defaultUserName = config('global.default_username');	
				$defaultPassword = config('global.default_password');

				if(	($userName==$defaultUserName)	&& ($password==$defaultPassword)){
					$responseValidation['validation'] = TRUE;
					$responseValidation['message'] = "Successfully Logged In";
					$defaultCredentials = true;
				}else{
				$responseValidation['validation'] = FALSE;
				$responseValidation['message'] = 'Invalid Login Credentials. Please try again';	
				}
			}
			else{
				$responseValidation['validation'] = FALSE;
				$responseValidation['message'] = 'Invalid Login Credentials.';
			}
		}
		else{
				$users[0] = (array) json_decode(json_encode($users[0]));

				if($users[0]['user_type'] == 'Super Admin' ){


				$responseValidation['validation'] = TRUE;
				$responseValidation['message'] = 'Successfully logged in';
				$responseUserInfo = $users[0];
				$userName = $responseUserInfo['userid'];
				}else{
					if($users[0]['email_verified_at']==NULL)	{
						$responseValidation['validation'] = FALSE;
						$responseValidation['message'] = 'Please Verify Your Email Address.';			
						$invalidEmail = true;
						}else{

						$responseValidation['validation'] = TRUE;
						$responseValidation['message'] = 'Successfully logged in';
						$responseUserInfo = $users[0];
						$userName = $responseUserInfo['userid'];	
						}
				}
		}
		$userIdExistCheck = DB::table('users')->whereRaw(" (email = '".$userName."' OR userid='".$userName."') ")->get()->toArray();
		if(count($userIdExistCheck)==0){
			$responseValidation['user_exists'] = false;
		}else{ $responseValidation['user_exists'] = true; }
		$responseValidation['user_info'] = $responseUserInfo;
		$responseValidation['default_credentials'] = $defaultCredentials;
		$responseValidation['input_user_name'] = $userName;
		$responseValidation['invalidEmail'] = $invalidEmail;
		return $responseValidation;
	}

}
class UserObj{
	private $userId,$userType,$name,$usersTableRow;

	private $permissions,$permissionsVal;
	private $userwiseDefaultPermissions;
	public $userRoles = array("Admin","Participant");
	function __construct($userId){
		$this->userId = $userId;
		
		$this->permissions=config('global.permissions');
		$this->permissionsVal=config('global.permissionsVal');

		$userDetails  = array();

		$users = DB::table('users')->whereRaw("(userid = '".$this->userId."')")->get()->toArray();
		if(count($users)!=0){
			$userDetails = (array) json_decode(json_encode($users[0]));
		}
		if(count($userDetails)>0)
		{
		$this->userId=$userId;
		$this->userType=$userDetails['user_type'];
		$this->name=trim($userDetails['first_name'].' '.$userDetails['last_name']);
		$this->usersTableRow=$userDetails;
		$this->setPermissions();
		}
		else{
			$this->userId="admin";
			$this->userType="Super Admin";
			$this->name="Administrator";
			$this->setPermissions();
			$this->usersTableRow=array();
		}

	}
	private function setPermissions(){

		if($this->userType=='Super Admin')
		{
			foreach ($this->permissions as $key=>$val)
			{
				$this->permissions[$key]=true;
// 				if($key=='editprojects'){$this->permissions[$key]=false;}
			}
		}
		else{
		    $permissionsOfUserArray = array();
		    $userSpecificPermissionArray = DB::table('user_specific_permissions_by_id')->whereRaw("(userid = '".$this->userId."')")->get()->toArray();
		    if(count($userSpecificPermissionArray)!=0){
				$userSpecificPermissionArray[0] = (array) json_decode(json_encode($userSpecificPermissionArray[0]));
			}

		    $userType =  $this->userType();
		    
		    $userDefaultPermissionsArray = DB::table('user_default_permissions_by_role')->whereRaw("(user_role = '".$userType."')")->get()->toArray();
		    if(count($userDefaultPermissionsArray)!=0){
				$userDefaultPermissionsArray[0] = (array) json_decode(json_encode($userDefaultPermissionsArray[0]));
			}

		    if(is_array($userSpecificPermissionArray) && (count($userSpecificPermissionArray)>0) )
		    {
		        $permissionsString = $userSpecificPermissionArray[0]['permissions_granted'];
		        if(strlen($permissionsString) > 0)
		        {
		            $permissionsOfUserArray = explode(",", $permissionsString);
		        }
		    }
		    elseif(is_array($userDefaultPermissionsArray) && (count($userDefaultPermissionsArray)>0) )
		    {
		        $permissionsString = $userDefaultPermissionsArray[0]['permissions_granted'];
		        if(strlen($permissionsString) > 0)
		        {
		            $permissionsOfUserArray = explode(",", $permissionsString);
		        }
		    }
		    foreach ($this->permissions as $key=>$val)
		    {
		        if(in_array($key, $permissionsOfUserArray))
		        {
		            $this->permissions[$key]=true;
		        }
		        // 				if($key=='editprojects'){$this->permissions[$key]=false;}
		    }
		}
	}

    public function userType()
    {
    	return $this->userType;
    }
    public function userFullName()
    {
    	return $this->name;
    }
    public function isUserAuthorized($authVar){
    	if(isset($this->permissions[$authVar])){
        return ($this->permissions[$authVar]);
    	}else{
    		return false;
    	}
    }
}
function checkAuthForPage($authPageId,$pageId,$autoForward = TRUE){

	// $superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();
	if(session()->has('logindata')){
		$loginData = session()->get('logindata');
		$loginCheckObj =  new UserObj($loginData['input_user_name']);

		if(($loginCheckObj->isUserAuthorized($authPageId) == false) && $autoForward == TRUE){

			$data['pageId'] = $pageId;
			$data['title'] = "Not Authorized";
			// return Redirect::to('/notauthorized');
			// return redirect("/notauthorized");
			return false;

		}else{
		return $loginCheckObj->isUserAuthorized($authPageId);
		}

	}else{

		return false;
	}
	
	// return $loginCheckObj->isUserAuthorized($authPageId);
}
function loggedInUserType(){
	// $superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();
	if(session()->has('logindata')){
		$loginData = session()->get('logindata');
		$loginCheckObj =  new UserObj($loginData['input_user_name']);

		return $loginCheckObj->userType();
	}else{
		return "";
	}
	
	// return $loginCheckObj->isUserAuthorized($authPageId);
}
function userProfilePic($userId){
	$userProfilePicPath = config('global.user_profile_pic_folder');
	$imgDetails = DB::table('user_meta')->select('*')->whereRaw('userid = \''.$userId.'\' AND attribute=\'profile pic\'')->limit(1)->get()->toArray();
	if(count($imgDetails)==0)
	{
		return "";
	}else{
		return (url("/")."/".$userProfilePicPath."/".$imgDetails[0]->value);
	}
}
function loggedInUserId(){
	if(session()->has('logindata')){
		$loginData = session()->get('logindata');
		return ($loginData['input_user_name']);
	}	
	else{
		return "";
	}
}

function userNameForID($userId){
	
		$user = new UserObj($userId);
		return ($user->userFullName());
	
}
function checkUserLogin($userName,$password)
{
	$loginCheckObj =  new LoginCheck();
	return $loginCheckObj->checkLogin($userName,$password);
}