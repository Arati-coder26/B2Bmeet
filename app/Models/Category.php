<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    //
    function saveCategoryToDB($input){
    	
    	$errors = array();
		$countryCheck = $this->where('category', '=', $input['categoryname'])->get()->toArray();
		$loggedInUser  = loggedInUserId();


		if(count($countryCheck)!=0){
		    	$errors[] = "Category Already Exists";
		    }
		if(checkAuthForPage('masterdatamanagement','categories') ==false){
			$errors[] = "You are not authorized";
		}

        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$this->category = $input['categoryname'];
			$this->description = "";
		    $this->save();
		    $response['validation'] = true;
		    $response['message'] = "Category Saved.";
		    }

    	

		    return $response;
    }

    function editCategoryToDB($input){
    	$countryId = $input['editCategoryId'];

    	$errors = array();
    	
    	$country = $this->find($countryId);

    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Category ID Passed";
    	}

    	$loggedInUser  = loggedInUserId();
    	if(checkAuthForPage('masterdatamanagement','categories') ==false){
			$errors[] = "You are not authorized";
		}
		
		$countryName = $input['editCategoryName'];
		if($countryName==""){
			$errors[] = "Category Name Cannot be blank";	
		}
		
		$validationCheck = DB::table('categories')->select('id')->whereRaw('LOWER(category)=\''.strtolower($countryName).'\' AND id!=\''. $countryId .'\'')->limit(1)->get()->toArray();
		
		
		if(count($validationCheck)!=0){
			$errors[] = "Category is already associated with another row";
		}
		


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$country->category = $countryName;
		    $country->save();
		    $response['validation'] = true;
		    $response['message'] = "Category Saved.";
		    }
		    return $response;
    }

    function deleteCategoryInDB($deleteCountryId){
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
		    $response['message'] = "Category has been deleted successfully.";
	    }
		    return $response;

    }
}
