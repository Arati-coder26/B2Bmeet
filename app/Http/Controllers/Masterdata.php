<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Country;
use App\Models\Cities;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;


class Masterdata extends Controller
{
    //
    function countriesmanagement(){
    	
    	$data['pagetitle'] = "Manage Countries";
        $data['pageId']  = 'countries';
        $country = new Country();
        $data['countries'] = $country->orderBy('country', 'asc')->get()->toArray();
    	return view("masterdata.countries",$data);
    }
    function countryeditandsave(Request $req){
            $loggedInUser = loggedInUserId();
            $country = new Country();
            
    	if($req->input('savecountry')){
            $response = $country->saveCountryToDB($req->input());
            return $response;
        }
        if($req->input('editcountry')){
            
            $response = $country->editCountryToDB($req->input());

            return $response;   
        }
        if($req->input('deleteCountryId')){

            $response = $country->deleteCountryInDB($req->input('deleteCountryId'));

            return $response;   
        }
    }

    function citiesmanagement($countryid = null){

        $data['pagetitle'] = "Manage Cities";
        $data['pageId']  = 'cities';
        $country = new Country();
        $citiesOfCountry = array();

        if($countryid!=null){
            $cityObj = new Cities();


            $countryName = "";
            $countryCheck = DB::table('countries')->where('id','=',$countryid)->get('*')->toArray();
                if(count($countryCheck)==0){
                    return redirect("/cities");
                }else{
                    $countryName = $countryCheck[0]->country;
                    $citiesOfCountry = $cityObj->where("country","=",$countryName)->get("*")->toArray();
                }


        }
        $data['countries'] = $country->orderBy('country', 'asc')->get()->toArray();
        $data['countryid'] = $countryid;
        $data['citiesOfCountry'] = $citiesOfCountry;
        return view("masterdata.cities",$data);

    }
    function cityeditandsave(Request $req){

        $city = new Cities();
        if($req->input('savecity')){
            $response = $city->saveCityToDB($req->input());
            return $response;
        }
        if($req->input('editCity')){
            $response = $city->editCityToDb($req->input());
            return $response;
        }
        if($req->input('deleteCityId')){
            $response = $city->deleteCityInDB($req->input('deleteCityId'));

            return $response;      
        }
        
    }
    function categoriesmanagement(){
            

        $data['pagetitle'] = "Manage Categories";
        $data['pageId']  = 'categories';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        return view("masterdata.categories",$data);        
    }
    function subcategorieseditandsave(Request $req){



        $city = new Subcategory();
        if($req->input('savesubcategory')){
            $response = $city->saveSavesubcategoryToDB($req->input());
            return $response;
        }
        if($req->input('editSubcategory')){
            $response = $city->editSubcategoryToDb($req->input());
            return $response;
        }
        if($req->input('deleteSubCategoryId')){
            $response = $city->deleteSubCategoryInDB($req->input('deleteSubCategoryId'));

            return $response;      
        }

    }
    function subcategoriesmanagement($categoryid = null){


        $data['pagetitle'] = "Manage Sub Categories";
        $data['pageId']  = 'subcategories';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        $citiesOfCountry = array();

        $subcategory = new Subcategory();
        $categoryName = "";
        if($categoryid != null){
        $countryCheck = DB::table('categories')->where('id','=',$categoryid)->get('*')->toArray();
                if(count($countryCheck)==0){
                    return redirect("/subcategories");
                }else{
                    $categoryName = $countryCheck[0]->category;
                    $citiesOfCountry = $subcategory->where('category',$categoryName)->orderBy('subcategory', 'asc')->get()->toArray();
                }

        }

        
        $data['categoryid'] = $categoryid;
        $data['citiesOfCountry'] = $citiesOfCountry;
        return view("masterdata.subcategories",$data);

        

    }
    function categoryeditandsave(Request $req)
    {

            $loggedInUser = loggedInUserId();
            $country = new Category();
            
        if($req->input('savecategory')){
            $response = $country->saveCategoryToDB($req->input());
            return $response;
        }
        if($req->input('editcategory')){
            
            $response = $country->editCategoryToDB($req->input());

            return $response;   
        }
        if($req->input('deleteCategoryId')){

            $response = $country->deleteCategoryInDB($req->input('deleteCategoryId'));

            return $response;   
        }   
    }
    function citySaveByUser(Request $req){

        $country = new Country();
        $citiesOfCountry = array();
        if($req->input('countryid')!=null && $req->input('cityName')!=null ){
            $cityObj = new Cities();


            $countryName = "";
            $countryCheck = DB::table('countries')->where('id','=',$req->input('countryid'))->get('*')->toArray();
            
                if(count($countryCheck)==0){
                    
                }else{
            $cityCheck = DB::table('cities')->whereRaw('LOWER(city) = \''. strtolower($req->input('cityName')).'\' AND country = \'' .$countryCheck[0]->country.'\'')->get('*')->toArray();
                    if(count($cityCheck)==0)
                    {
                        $loggedInUser = loggedInUserId();
                        $cityObj->country = $countryCheck[0]->country;
                        $cityObj->state_id = "";
                        $cityObj->city = $req->input('cityName');
                        $cityObj->created_by = $loggedInUser;
                        $cityObj->updated_by = $loggedInUser;
                        $cityObj->save();
                    }
                    

                }
        if(count($countryCheck)!=0){
                $cityCheck = DB::table('cities')->whereRaw('LOWER(city) = \''. strtolower($req->input('cityName')).'\' AND country = \'' .$countryCheck[0]->country.'\'')->get('*')->toArray();
                if(count($cityCheck)!=0){
                    $response['city_id'] = $cityCheck[0]->id;
                    $response['city_name'] = $cityCheck[0]->city;

                    return $response;
                }
            }
        }
    }

    function adminCityAction(Request $req){
        if($req->ajax())
        {
            $cityRowId = $req->input('cityRowId');
            $action = $req->input('cityAction');
            if($action == 'approve')
            {
                $cityObj = new Cities();

                $city = $cityObj->find($cityRowId);
                if($city!=false && $city!=null){
                    $userId = loggedInUserId();
                    $city->updated_by = $userId;
                    $city->created_by = $userId;
                    $city->save();
                }
                $response['validation'] = true;
                $response['message'] = "City Has been approved";
            }
            if($action == 'delete'){

                $cityObj = new Cities();

                $city = $cityObj->find($cityRowId);
                if($city!=false && $city!=null){
                    $city->delete();
                }
                $response['validation'] = true;
                $response['message'] = "City Has been deleted";
            }
            if($action == 'edit'){

                $cityObj = new Cities();

                $city = $cityObj->find($cityRowId);
                if($city!=false && $city!=null){
                    
                    $userId = loggedInUserId();
                    $city->updated_by = $userId;
                    $city->created_by = $userId;
                    $city->city = $req->input('cityName');
                    $city->save();

                }
                $response['validation'] = true;
                $response['message'] = "City Has been Updated";
            }

            return $response;
        }

    }
}
