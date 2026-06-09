<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function createNewUserSuperAdmin($req){
        
        $errors = array();

            $superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();
            if(count($superAdminCheck)!=0)
            {
                $errors[] = "Super Admin Account is already registered with us";
            }

            $superAdminCheck = DB::table('users')->where('phone_number',$req['phonenumber'])->get()->toArray();
            if(count($superAdminCheck)!=0)
            {
                $errors[] = "Phone Number is already in use";
            }


            $superAdminCheck = DB::table('users')->where('email',$req['emailaddress'])->get()->toArray();
            if(count($superAdminCheck)!=0)
            {
                $errors[] = "Email Address is already in use";
            }
            $superAdminCheck = DB::table('users')->where('userid',$req['username'])->get()->toArray();
            if(count($superAdminCheck)!=0)
            {
                $errors[] = "User ID is already in use";
            }



        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
        $this->first_name = $req['firstName'];
        $this->last_name = $req['lastName'];
        $this->userid = strtolower($req['username']);
        $this->email = $req['emailaddress'];
        $this->password = md5($req['userpassword']);
        $this->phone_number = $req['phonenumber'];
        $this->profile_completion_status = "Complete";
        if($req['dateofbirth']==NULL){
            $req['dateofbirth'] = "";
        }
        $this->email_verified_at = date("Y-m-d H:i:s");
        $this->date_of_birth = $req['dateofbirth'];
        $this->user_type = 'Super Admin';
        $this->email_verification_token = "";
        $this->save();

        $response['validation']  = true;
        $response['message'] = "Super Admin Profile has been successfully saved";

            $inputdata['loginCheck'] =$loginCheck = checkUserLogin($req['username'],$req['userpassword']);
            if($loginCheck['validation']==false){
                // return view("signup.login",$inputdata);
            }else{
                $totalTimeVal = intval(config('global.session_logout_time'));
                $logoutTime = date("Y-m-d H:i:s",time()+($totalTimeVal));
                $loginCheck['login_valid_upto'] = $logoutTime;
                session()->put('logindata',$loginCheck);    
                \Session::save();
            } 
        }
        return $response;
    }
    function createNewParticipant($req){

            $errors = array();


            $superAdminCheck = DB::table('users')->where('user_type','Super Admin')->get()->toArray();
            if(count($superAdminCheck)==0)
            {
                $errors[] = "Super Admin Profile has not yet been created. You cannot register the profile right now.";
            }
            $validationCheck = DB::table('users')->where('email',$req['email'])->get()->toArray();
            if(count($validationCheck)!=0)
            {
                $errors[] = "Email Address is already in use";
            }

            if($req['userid']!=''){
                    $finalUserId = $req['userid'];
            }
            if(isset($req['registrationwithevent']) && $req['registrationwithevent']!=NULL){
                    // echo "User id is ".$req['userid'];
                    if($req['userid']==''){

                       $nameCombination = strtolower($req['first_name']).strtolower($req['last_name']);
                       $nameCombination = preg_replace("/[^a-z_.\-0-9]/i", "", $nameCombination);
                        $nameCombinationCheck = DB::table('users')->where('userid',$nameCombination)->get()->toArray();           
                        if(count($nameCombinationCheck)==0){
                            $finalUserId = $nameCombination;
                        }
                        else{
                            $nameCombination = strtolower($req['first_name'])."_".strtolower($req['last_name']);
                            $nameCombination = preg_replace("/[^a-z_.\-0-9]/i", "", $nameCombination);
                            $nameCombinationCheck = DB::table('users')->where('userid',$nameCombination)->get()->toArray();           
                            if(count($nameCombinationCheck)==0){
                                $finalUserId = $nameCombination;
                            }else{
                                    $nameCombination = strtolower($req['first_name']).".".strtolower($req['last_name']);
                                    $nameCombination = preg_replace("/[^a-z_.\-0-9]/i", "", $nameCombination);
                                    $nameCombinationCheck = DB::table('users')->where('userid',$nameCombination)->get()->toArray();           
                                    if(count($nameCombinationCheck)==0){
                                        $finalUserId = $nameCombination;
                                    }else{
                                        do{
                                                $nameCombination = strtolower($req['first_name']).strtolower($req['last_name']);
                                                $nameCombination = preg_replace("/[^a-z_.\-0-9]/i", "", $nameCombination).(random_generator(4,'numeric'));
                                                $nameCombinationCheck = DB::table('users')->where('userid',$nameCombination)->get()->toArray();

                                        }while(count($nameCombinationCheck)!=0);
                                        $finalUserId = $nameCombination;
                                    }
                            }
                        }

                    }
            }

            $validationCheck = DB::table('users')->where('userid',strtolower($finalUserId))->get()->toArray();
            if(count($validationCheck)!=0)
            {
                $errors[] = "User Id is already in use";
            }
            $enteredUserId = $finalUserId;
            if(preg_match("/[^a-z_.\-0-9]/i", strtolower($finalUserId)))
            {
                    $errors[] = "Only small case alphabets,numbers are allowed in the user id";
                    
            }
            $firstName = $req['first_name'];
            $lastName = $req['last_name'];
            $password = $req['password'];

            if($firstName =="" ){ $errors[] = "First Name cannot be blank" ;}
            if($lastName =="" ){ $errors[] = "Last Name cannot be blank" ;}
            if($password =="" ){ $errors[] = "Password Name cannot be blank" ;}


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->userid = strtolower($finalUserId);
        $this->email = $req['email'];
        $this->password = md5($password);
        $this->phone_number = "";
        $this->profile_completion_status = "Incomplete";
        
        $this->date_of_birth = "";
        $this->user_type = 'Participant';
        $this->email_verification_token = $emailVerificationCode = random_generator(35,'alphanumeric');
        $this->save();

            $userNamePasswordContent = "<br />";
        if(isset($req['registrationwithevent']) && $req['registrationwithevent']!=NULL){
                $eventId = $req['registrationwithevent'];


            DB::table('virtual_event_participants')->insert(
                array('userid' => $finalUserId,
    'event_id' => $req['registrationwithevent'], 'approval_status' => 'Accepted', 'created_at' => (date("Y-m-d H:i:s")), 'updated_at' => (date("Y-m-d H:i:s"))

                ));

            if(isset($req['autogeneratedpassword']) && $req['autogeneratedpassword']!=NULL && $req['autogeneratedpassword']!="")
            {
                    $userNamePasswordContent .= "<table><tr><th colspan=\"2\">Your System Generated Login Credentials</th></tr><tr><td>Username</td><td>".$finalUserId."</td></tr><tr><td>Password</td><td>".$password."</td></tr></table>";
            }
        }
        $response['validation']  = true;
        $response['message'] = "Profile has been registered. Please Verify Your email address by clicking on the link."; 

        $mailObj['to_address'] = $req['email'];
        $mailObj['to_name'] = trim($firstName." ".$lastName);
        $mailObj['subject'] = "Email Verification for new account ".(config('global.site_name'));
        $emailVerificationLink = url('/register/verifyemail/')."/".$emailVerificationCode."/".urlencode($finalUserId);
        $mailObj['htmlcontent'] = "Please <a href='".$emailVerificationLink."'>click here</a> for Email verification.<br />If you are unable to open the link please copy and paste the following link in the browser <br /><b>".$emailVerificationLink."</b>".$userNamePasswordContent;

        sendHtmlEmail($mailObj);

        }
        return $response;

    }
    function verifyUser($id){
        $user = $this->find($id);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->email_verification_token = "";
        $user->save();
    }
    function recoverPasswordForEmail($email)
    {
        $errors = array();
         $user = $this->select('*')->where('email', $email)->get()->toArray();
              
                 if(count($user)==0){
                    $errors[] = "Invalid Email address sent";
                 }
         if(count($errors)!=0){

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;


         }else{
        $newPassword = random_generator(7,'numeric');
        $response['validation']  = true;
        $response['message'] = "New Password has been sent to your registered email address."; 

        $mailObj['to_address'] = $user[0]['email'];
        $mailObj['to_name'] = trim($user[0]['first_name']." ".$user[0]['last_name']);
        $mailObj['subject'] = "New Password for your account - ".(config('global.site_name'));
        $mailObj['htmlcontent'] = "Please login with your newly generated password.<br />User ID : <b>".$user[0]['userid']."</b><br />Password : <b>".$newPassword."</b>";


            $user = $this->find($user[0]['id']);
        $user->password = md5($newPassword);
        $user->email_verification_token = "";
        $user->save();

        sendHtmlEmail($mailObj);

            }

        return $response;
    }
    public function editPersonalInfo($input){

        $userId = $input['update_profile_id'];

        $errors = array();

        if($input['first_name'] == ""){
            $errors[] = "First Name Cannot Be blank";
        }
        if($input['last_name']==""){
            $errors[] = "Last Name Cannot Be blank";
        }

            $user = $this->select('*')->where('userid', $userId)->get()->toArray();
              
                 if(count($user)==0){
                    $errors[] = "Invalid User ID";
                 }

        if(count($errors)==0){

            $user = $this->find($user[0]['id']);


                    $user->first_name = $input['first_name'];
                    $user->last_name = $input['last_name'];
                    $user->phone_number = ( $input['phonenumber']==NULL ? "" : $input['phonenumber'] );
                    $user->country = $input['country'];
                    $user->city = $input['city'];
                    $user->video_embed_link = $input['videoembed'];
                    $user->save();


        }

    }
    public function editPassword($input){
        $newPassword = $input['newpassword'];
        $repeatPassword =  $input['repeatpassword'];
        if($newPassword!=$repeatPassword){
            session()->flash('password_update_error', 'New Password and repeat password donot match.');
            return false;
        }elseif(strlen($newPassword)<6){
            $request->session()->flash('password_update_error', 'Password must be atleast 6 characters long');
            return false;
        }else{


                $user = $this->select('*')->where('userid', loggedInUserId())->get()->toArray();

                if(count($user)==1)
                {
                    $user = $this->find($user[0]['id']);
                        $user->password = md5($newPassword);
                    $user->save();
                }
                return true;
        }
    }
    public static function checkAuthForPage($authPageId,$pageId,$autoForward = TRUE){
         

        if(checkAuthForPage('createevents','createevents')){
            return true;
        }else{
            Redirect::to('/notauthorized');
        }
    }

}
