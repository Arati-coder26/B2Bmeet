<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


use App\User;
use App\Models\UserProfessionalInfo;

class Profiles extends Controller
{
    public function profile(Request $req){

    	$data = $req->session()->get('logindata');
    	return view('accountpages.profile',['data' =>$data]);
    }
    public function logout(){
    	session()->forget('logindata');
		return redirect("/login");
    }
    public function task(){

    	$data = session()->get('data');
    	return view('accountpages.task',['data' =>$data]);
    }
    public function taskInput(Request $req){

    	$data = session()->get('data');
    	$input = $req->input();
    	$req->session()->flash('status',"My Task ".$req->input('task'));
    	return view('accountpages.task',['data' =>$data , 'input' => $input]);
    }
    public function fileUpload(){
        return view('accountpages.filupload');
    }
    public function fileUploadWithFile(Request $req){

        $path = $req->file('imgUpload1')->store('myCustomFolder');
        $data = array('path'=>$path);
        return view('accountpages.filupload',$data);
    }
    function fetchUserRecords(){
        $dbPrefix = DB::getTablePrefix();
        echo $dbPrefix."<br />";
        return DB::select('select * FROM users');
    }
    public function dashboardView(){

            $data['pagetitle'] = "Dashboard - ".(config('global.site_name'));
            $data['meta_description'] = "description";
            if(loggedInUserType()=='Participant')
            {
                return view('accountpages.participantdashboardView',$data);        
            }else if(loggedInUserType()=='Super Admin'){
        return view('accountpages.superadmin_dashboard',$data);
            }else{
        return view('accountpages.dashboardView',$data);
            }
    }
    public function createsuperadminprofile(){


        $superAdminDetails = User::where('user_type', '=', 'Super Admin')->get()->toArray();;
        if(count($superAdminDetails)==0){
        $data['pagetitle'] = "Super Admin Profile Registrations";
        $data['meta_description'] = "description";
        return view("accountpages.superadminprofile",$data);
        }else{
            return redirect("/dashboard");
        }
    }
    public function savesuperadminprofile(Request $req){
        $userReg = new User;

        $response = $userReg->createNewUserSuperAdmin($req->input());
            return $response;
    }
    function updateProfileInfo(){
        

            $data['pagetitle'] = "Update Profile Information";
            $data['meta_description'] = "profileinformation";
            $data['pageid'] = 'dashboardview';
            $country = new \App\Models\Country();
                $data['countries'] = $country->orderBy('country', 'asc')->get()->toArray();
            return view("accountpages.updateprofile",$data);

    }
    function userProfile($userid=NULL){
        if($userid == NULL){
            return redirect("/dashboard");
        }
        $data['primaryInfo'] = DB::table('users')->where('userid',$userid)->get()->toArray();
        $data['professionalInfo'] = DB::table('user_professional_info')->where('userid',$userid)->get()->toArray();
        $data['metaInfo'] = DB::table('user_meta')->where('userid',$userid)->get()->toArray();
        $nameOfUser = "";
        if(count($data['primaryInfo']) !=0 )
        {
            $nameOfUser = $data['primaryInfo'][0]->first_name . " ". $data['primaryInfo'][0]->last_name."-";
        }

            $data['pagetitle'] = $nameOfUser."Profile Information";
            $data['meta_description'] = "Profile Information";
            $data['pageid'] = '';
        return view("accountpages.profile",$data);
        
    }
    function updateProfileSave(Request $req){

        if($req->input('updateprofessionalinfo') &&  $req->input('updateprofessionalinfo')!=""){
            $professionalInfoObj = new UserProfessionalInfo();
            $userObj = new User();
            $userObj->editPersonalInfo($req->input());
            return $professionalInfoObj->saveProfessionalInfo($req->input());

        }

    }
    function pendingRequests($type = null){
        
        $data['type'] = "All";
        if($type=='virtual_events'){

            $data['type'] = 'virtual_events';
        }
            $data['pagetitle'] = "Pending Requests";
            $data['meta_description'] = "Pending requests";
            $data['pageId'] = 'pendingrequests';
        return view("notifications.pendingnotifications",$data);
    }
    function scheduledEvents($type = null){
        
        $data['type'] = "All";
        if($type=='virtual_events'){

            $data['type'] = 'virtual_events';
        }
            $data['pagetitle'] = "Schedule";
            $data['meta_description'] = "Schedule";
            $data['pageId'] = 'schedule';
        return view("accountpages.schedule",$data);
    }
    function editPassword(Request $req){

            $data['pagetitle'] = "Edit Password";
            $data['meta_description'] = "Edit Password";
            $data['pageId'] = 'editpassword';

            if($req->input('newpassword')!=NULL && $req->input('repeatpassword')!=NULL)
            {
                $userObj = new User();
                $response = $userObj->editPassword($req->input());

                $data['response'] = $response;
            }
        return view("accountpages.editpassword",$data);   
    }
/*
    function imageupload(Request $request)
    {
     
     return $request->input();

     if($request->ajax())
     {
      $image_data = $request->image;
      $image_array_1 = explode(";", $image_data);
      $image_array_2 = explode(",", $image_array_1[1]);
      $data = base64_decode($image_array_2[1]);
      $image_name = time() . '.png';
      $upload_path = public_path('crop_image/' . $image_name);
      file_put_contents($upload_path, $data);
      return response()->json(['path' => '/crop_image/' . $image_name]);
     }
    }*/
}

