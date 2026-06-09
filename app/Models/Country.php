<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Country extends Model
{
    //
    function saveCountryToDB($input){
    	
    	$errors = array();
		$countryCheck = $this->where('country', '=', $input['countryname'])->get()->toArray();
		$loggedInUser  = loggedInUserId();

		if(count($countryCheck)!=0){
		    	$errors[] = "Country Already Exists";
		    }
		if(checkAuthForPage('masterdatamanagement','countries') ==false){
			$errors[] = "You are not authorized";
		}

        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$this->country = $input['countryname'];
		    $this->created_by = $loggedInUser;
		    $this->save();
		    $response['validation'] = true;
		    $response['message'] = "Country Saved.";
		    }

    	

		    return $response;
    }
    function editCountryToDB($input){
    	$countryId = $input['editCountryId'];
    	$errors = array();
    	
    	$country = $this->find($countryId);
    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Country ID Passed";
    	}

    	$loggedInUser  = loggedInUserId();
    	if(checkAuthForPage('masterdatamanagement','countries') ==false){
			$errors[] = "You are not authorized";
		}
		
		$countryName = $input['editCountryName'];
		if($countryName==""){
			$errors[] = "Country Name Cannot be blank";	
		}
		
		$validationCheck = DB::table('countries')->select('id')->whereRaw('LOWER(country)=\''.strtolower($countryName).'\' AND id!=\''. $countryId .'\'')->limit(1)->get()->toArray();
		
		if(count($validationCheck)!=0){
			$errors[] = "Country name already associated with another row";
		}
		


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$country->country = $countryName;
		    $country->created_by = $loggedInUser;
		    $country->save();
		    $response['validation'] = true;
		    $response['message'] = "Country Saved.";
		    }
		    return $response;
    }
    function deleteCountryInDB($deleteCountryId){
		$errors = array();
    	
    	$country = $this->find($deleteCountryId);
    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Country ID Passed";
    	}


    	
        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
		    $country->delete();
		    $response['validation'] = true;
		    $response['message'] = "Country has been deleted successfully.";
	    }
		    return $response;

    }
}
