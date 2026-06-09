<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Cities extends Model
{
    //
    protected $table="cities" ;

    function saveCityToDB($input){
    	$errors = array();
		$countryCheck = $this->where('city', '=', $input['cityname'])->get()->toArray();
		$loggedInUser  = loggedInUserId();

		if(count($countryCheck)!=0){
		    	$errors[] = "City Already Exists";
		    }
		if(checkAuthForPage('masterdatamanagement','countries') ==false){
			$errors[] = "You are not authorized";
		}


		$countryName = "";
			$countryCheck = DB::table('countries')->where('id','=',$input['selectedcountryid'])->get('*')->toArray();
		if(count($countryCheck)==0){
			$errors[] = "Invalid Country ID Passed";
		}else{
			$countryName = $countryCheck[0]->country;
		}


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$this->city = $input['cityname'];
			$this->country = $countryName;
		    $this->created_by = $loggedInUser;
			$this->updated_by = $loggedInUser;		    
		    $this->state_id = "";
		    $this->save();
		    $response['validation'] = true;
		    $response['message'] = "City Saved.";
		    }

    	

		    return $response;
    }
    function editCityToDb($input){

    	$countryId = $input['editCityId'];
    	$errors = array();
    	$country = $this->find($countryId);

    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Country ID Passed";
    	}

    	$loggedInUser  = loggedInUserId();
    	if(checkAuthForPage('masterdatamanagement','cities') ==false){
			$errors[] = "You are not authorized";
		}
		
		$countryName = $input['editCityName'];
		if($countryName==""){
			$errors[] = "City Name Cannot be blank";	
		}
		
		$validationCheck = DB::table('cities')->select('id')->whereRaw('LOWER(city)=\''.strtolower($countryName).'\' AND id!=\''. $countryId .'\'')->limit(1)->get()->toArray();
		
		if(count($validationCheck)!=0){
			$errors[] = "City name already associated with another row";
		}
		


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$country->city = $countryName;
		    $country->updated_by = $loggedInUser;
		    $country->save();
		    $response['validation'] = true;
		    $response['message'] = "City Saved.";
		    }
		    return $response;
    }
    function deleteCityInDB($deleteCountryId){
		$errors = array();
    	$country = $this->find($deleteCountryId);
    	if($country == false || ($country==null)){
    		$errors[] = "Invalid City ID Passed";
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
