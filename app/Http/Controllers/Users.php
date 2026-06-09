<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

 use Cookie;

use Illuminate\Support\Facades\Storage; 
use File;
use Illuminate\Support\Facades\DB;


class Users extends Controller
{
    //
    function index(){
    	// return array("firstname"=>"Venkata","lastname"=>"Srinath");
    	$resp = Http::get("https://www.srpointofsale.com/");
    	print_r($resp);
    	// return "My Name is venkata srinath";
    }
    function show($id){
    	return view('testviews.firstview',array('id'=>$id,'date'=>	(date("Y-m-d H:i:s")))	);
    }

	function formHandling(Request $req)    {
		// return $req->input();
		$req->validate([
			'email' => 'required | email',
			'password' => 'required | min:5'
		]);
		// echo $req->url();
		// return $req->input('email');
		// return $req->input();
		// echo $req->path();
		// echo $req->fullurl();
		$req->method();
		return $req->input();
	}
	function setLanguageLocal(Request $req){
		$language = $req->input('language');
		
		echo "Language Preference Has Been Saved";

		Cookie::queue('language_selected', $language, 60*60*24*3);

	}
	function deleteUser(Request $req){
		// if($req->ajax())
		{

			if(loggedInUserId() == $req->input('deleteUserId'))
			{
				$errors = array();
				$errors[] = "You cannot delete your own user ID";
				$response['validation'] = false;
				$response['message'] = "Found an error";
				$response['errors'] = $errors;
				return $response;


			}
$profilePicDirectoryName = config('global.user_profile_pic_folder');
			$response['validation'] = true;
			$response['message'] = "User Profile deleted successfully";
      $imgDetails = DB::table('user_meta')->select('*')->whereRaw('userid = \''. ($req->input('deleteUserId')) .'\' AND attribute=\'profile pic\'')->limit(1)->get()->toArray();
      
      if(count($imgDetails)!=0)
      {
      	$imgName = $imgDetails[0]->value;
      	$imageTotalPath = public_path($profilePicDirectoryName.'/'.$imgName);
      	
      	
      	if(\File::exists(public_path($profilePicDirectoryName.'/'.$imgName))){
      		\File::delete(public_path($profilePicDirectoryName.'/'.$imgName));

		  }

		  if(\File::exists(public_path($profilePicDirectoryName.'\\'.$imgName))){
			\File::delete(public_path($profilePicDirectoryName.'\\'.$imgName));

		  }
		  
		  DB::table('user_meta')->where('id', $imgDetails[0]->id)->delete();
		
      }

			\DB::table('virtual_event_participants')->where('userid',$req->input('deleteUserId'))->delete();
			\DB::table('virtual_event_requests')->whereRaw('participant1_userid=\''.$req->input('deleteUserId').'\' OR participant2_userid=\''.$req->input('deleteUserId').'\'')->delete();
			\DB::table('virtual_meeting_chat')->whereRaw('sender_id=\''.$req->input('deleteUserId').'\' OR receiver_id=\''.$req->input('deleteUserId').'\'')->delete();

			\DB::table('virtual_meeting_chat')->whereRaw('sender_id=\''.$req->input('deleteUserId').'\' OR receiver_id=\''.$req->input('deleteUserId').'\'')->delete();
			
			\DB::table('virtual_event_meetings')->whereRaw('participant1_userid=\''.$req->input('deleteUserId').'\' OR participant2_userid=\''.$req->input('deleteUserId').'\'')->delete();
			
			\DB::table('connections')->whereRaw('userid1=\''.$req->input('deleteUserId').'\' OR userid2=\''.$req->input('deleteUserId').'\'')->delete();
			\DB::table('users')->where('userid',$req->input('deleteUserId'))->delete();
			
			return $response;
		}
	}
}
