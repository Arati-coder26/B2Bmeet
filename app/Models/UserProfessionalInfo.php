<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\User;

class UserProfessionalInfo extends Model
{
    //
    protected $table = 'user_professional_info';
    

    function saveProfessionalInfo($input){

    	$userId = $input['update_profile_id'];
    	$usercompany = $input['company'];
    	$errors = array();

    	if($usercompany == ""){
    		$errors[] = "Company Name Cannot Be blank";
    	}
    	if($input['designation']==""){
    		$errors[] = "Designation Cannot Be blank";
    	}
    	if($input['briefintroduction']==""){
    		$errors[] = "Introduction is mandatory";
    	}

    	if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$categoriesOfInterestString = "";
        	if(isset($input['categoryOfInterest']) && ($input['categoryOfInterest']!=FALSE && $input['categoryOfInterest']!=NULL)) {
        		$categoriesOfInterestString = implode(",", $input['categoryOfInterest']);
        	}


			$subcategoriesOfInterestString = "";
        	if(isset($input['subCategoriesOfInterest']) && ($input['subCategoriesOfInterest']!=FALSE && $input['subCategoriesOfInterest']!=NULL)	)	{
        		$subcategoriesOfInterestString = implode(",", $input['subCategoriesOfInterest']);
        	}


    		$userExistingInfo = $this->where('userid','=',$userId)->get()->toArray();
            
    		if(count($userExistingInfo)==0){
    			// insert
    			$this->userid = $userId;
    			$this->designation = $input['designation'];
    			$this->company = $input['company'];
    			$this->about_me = $input['briefintroduction'];
    			$this->categories_of_interest = $categoriesOfInterestString;
    			$this->subcategories_of_interest = $subcategoriesOfInterestString;
    			$this->business_category = $input['categoryOfBusiness'];
    			$this->subcategory = $input['subcategoryOfBusiness'];
    			$this->convenient_timings = $input['availabletimings'];

    			$this->save();
    		}
    		else{
    			// update
    			$professionalInfoObj = $this->find($userExistingInfo[0]['id']);

    			$professionalInfoObj->userid = $userId;
    			$professionalInfoObj->designation = $input['designation'];
    			$professionalInfoObj->company = $input['company'];
    			$professionalInfoObj->about_me = $input['briefintroduction'];
    			$professionalInfoObj->categories_of_interest = $categoriesOfInterestString;
    			$professionalInfoObj->subcategories_of_interest = $subcategoriesOfInterestString;
    			$professionalInfoObj->business_category = $input['categoryOfBusiness'];
    			$professionalInfoObj->subcategory = $input['subcategoryOfBusiness'];
    			$professionalInfoObj->convenient_timings = $input['availabletimings'];

    			$professionalInfoObj->save();


    		}
			
            $profileInfo = DB::table('users')->where('userid',$userId)->get()->toArray();
            $userRowId = "";
            if(is_array($profileInfo) && count($profileInfo)>0){
            	$userRowId = $profileInfo[0]->id;
            }
            $userObj = User::find($userRowId);
            if($userObj!=NULL && $userObj!=FALSE){
            $userObj->profile_completion_status	= 'Complete';
            $userObj->save();
            }
    		// 
		    $response['validation'] = true;
		    $response['message'] = "Professional information saved.";
        }
        return $response;

    }
}
