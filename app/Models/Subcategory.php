<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Subcategory extends Model
{
    //
    function saveSavesubcategoryToDB($input){
    	$errors = array();

		
		$loggedInUser  = loggedInUserId();



		$countryName = "";
			$countryCheck = DB::table('categories')->where('id','=',$input['selectedcategoryid'])->get('*')->toArray();
		if(count($countryCheck)==0){
			$errors[] = "Invalid Category ID Passed";
		}else{
			$countryName = $countryCheck[0]->category;
		}
		
		$countryCheck = $this->whereRaw('subcategory=\''. $input['subcategoryname'].'\' AND category=\''.$countryName.'\'')->get()->toArray();
		if(count($countryCheck)!=0){
		    	$errors[] = "Subcategory Already Exists under ".$countryName;
		    }
		if(checkAuthForPage('masterdatamanagement','subcategories') ==false){
			$errors[] = "You are not authorized";
		}


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$this->subcategory = $input['subcategoryname'];
			$this->category = $countryName;
		    $this->created_by = $loggedInUser;
			$this->updated_by = $loggedInUser;		
		    $this->save();
		    $response['validation'] = true;
		    $response['message'] = "Sub Category Saved.";
		    }

    	

		    return $response;
    }
    function editSubcategoryToDb($input){

    	$countryId = $input['editSubcategoryId'];
    	$errors = array();
    	$country = $this->find($countryId);
    	$categoryName = $country->category;

    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Sub Category ID Passed";
    	}

    	$loggedInUser  = loggedInUserId();
    	if(checkAuthForPage('masterdatamanagement','subcategories') ==false){
			$errors[] = "You are not authorized";
		}
		
		$countryName = $input['editSubcategoryName'];
		if($countryName==""){
			$errors[] = "Sub Category Name Cannot be blank";	
		}
		
		$validationCheck = DB::table('subcategories')->select('id')->whereRaw('LOWER(subcategory)=\''.strtolower($countryName).'\' AND id!=\''. $countryId .'\' AND category=\''.$categoryName.'\'')->limit(1)->get()->toArray();
		
		if(count($validationCheck)!=0){
			$errors[] = "Sub Category name already associated with another row";
		}
		


        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$country->subcategory = $countryName;
		    $country->updated_by = $loggedInUser;
		    $country->save();
		    $response['validation'] = true;
		    $response['message'] = "Sub Category Saved.";
		    }
		    return $response;
    }
    function deleteSubCategoryInDB($deleteCountryId){
		$errors = array();
    	$country = $this->find($deleteCountryId);
    	if($country == false || ($country==null)){
    		$errors[] = "Invalid Sub Category ID Passed";
    	}


    	
        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
		    $country->delete();
		    $response['validation'] = true;
		    $response['message'] = "Sub Category has been deleted successfully.";
	    }
		    return $response;

    }
}
