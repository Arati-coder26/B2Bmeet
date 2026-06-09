<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use File;


use App\User;
use App\Models\Usermeta;

use Illuminate\Support\Facades\DB;

class ImageCropController extends Controller
{
    //

    function upload(Request $request)
    {
     // if($request->ajax())
     {

     $profilePicDirectoryName = config('global.user_profile_pic_folder');
      $userId = $request->userid;

      $imgDetails = DB::table('user_meta')->select('*')->whereRaw('userid = \''.$userId.'\' AND attribute=\'profile pic\'')->limit(1)->get()->toArray();
      
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

      $image_data = $request->image;
      $image_array_1 = explode(";", $image_data);
      $image_array_2 = explode(",", $image_array_1[1]);
      $data = base64_decode($image_array_2[1]);
      $filteredUserId = preg_replace("/[^A-Za-z0-9]/", '', $request->userid);
      $image_name = $filteredUserId."_".time() . '.png';
      $upload_path = public_path($profilePicDirectoryName.'/' . $image_name);


   

    if(!File::isDirectory(public_path($profilePicDirectoryName.'/'))){

        File::makeDirectory(public_path($profilePicDirectoryName.'/'), 0777, true, true);

    }
      //$path = $request->file('image')->store('avatars');
      
      file_put_contents($upload_path, $data);
      $userId = $request->userid;

      $userMetaImg = new Usermeta();
      $userMetaImg->userid = $userId;
      $userMetaImg->attribute = 'profile pic';
      $userMetaImg->value = $image_name;
      $userMetaImg->save();

      return response()->json(['path' => (url('/')).'/'.$profilePicDirectoryName.'/' . $image_name]);
     }
    }
}
