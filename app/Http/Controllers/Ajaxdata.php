<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Category;
use App\Models\Subcategory;

use App\Models\Country;
use App\Models\Cities;

use Illuminate\Support\Facades\DB;

class Ajaxdata extends Controller
{
    //
    public function subcategoriesForCategory($categoryid){
    	$categories = explode(",", $categoryid);
    	$opArray = array();
    	if(is_array($categories) && count($categories)>0)
    	{
    		foreach($categories as $categoryid){

    			$categoryObj = Category::find($categoryid);
		    	if($categoryObj==NULL || $categoryObj==FALSE){
		    		$array = array();
		    		// array_push($opArray, $array)
		    	}else{
		    		$categorySelected = $categoryObj->category;
		    		$subCategoryObj = new Subcategory();
		    		$subCategoriesList = $subCategoryObj->where('category','=',$categorySelected)->orderBy('subcategory')->get()->toArray();
		    		// array_push($opArray, $subCategoriesList);
		    		$opArray = array_merge($opArray, $subCategoriesList);

		    	}

    		}

    	}

    	return $opArray;
    	
    }
    public function citiesForCountry($countryid){

        $countries = explode(",", $countryid);
        $opArray = array();
        if(is_array($countries) && count($countries)>0)
        {
            foreach($countries as $country){

                $countryObj = Country::find($country);
                if($countryObj==NULL || $countryObj==FALSE){
                    $array = array();
                    // array_push($opArray, $array)
                }else{
                    $allAdmins = DB::table('users')->whereRaw('user_type =\'Super Admin\' OR user_type=\'Admin\'')->get()->toArray();
                    if(count($allAdmins)!=0){
                        $whereInCondition = "";
                        for($k=0;$k<count($allAdmins);$k++){
                            $whereInCondition = "'".$allAdmins[$k]->userid."',";
                        }
                        $whereInCondition = rtrim($whereInCondition,",");


                    $categorySelected = $countryObj->country;
                    $citiesObj = new Cities();
                    if($whereInCondition!="")
                    {
                        $subCategoriesList = $citiesObj->whereRaw('country = \''.$categorySelected.'\' ')->orderBy('city')->get()->toArray();
                        //AND updated_by IN ('.$whereInCondition.')
                    }else{
                    $subCategoriesList = $citiesObj->where('country','=',$categorySelected)->orderBy('city')->get()->toArray();
                    }
                    // array_push($opArray, $subCategoriesList);
                    }else{ $subCategoriesList = array(); }
                    $opArray = array_merge($opArray, $subCategoriesList);

                }

            }

        }

        return $opArray;
    }
}
