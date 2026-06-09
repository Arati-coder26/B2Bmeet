<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Country;
use App\Models\Cities;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Virtualevent;
use Illuminate\Support\Facades\DB;


class SearchProfiles extends Controller
{
    //
    function index(){

        $data['pagetitle'] = "Search Profiles";
        $data['pageId']  = 'searchprofiles';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        $citiesOfCountry = array();

        $subcategory = new Subcategory();
        $categoryName = "";
        $data['paginationpageid'] = '1';
        return view("profilessearchandview.searchprofiles",$data);

    }
    function eventProfiles($eventid,Request $req){
        $virtualEvent = new Virtualevent();
        $eventInfo = $virtualEvent->where('event_id',$eventid)->get()->toArray();
        if(count($eventInfo)==0){
            return redirect("/dashboard");
        }else{
                
                $data['pagetitle'] = "Search Profiles for ".$eventInfo[0]['event_title'];
        $data['pageId']  = 'searchprofiles';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        $citiesOfCountry = array();
            $data['eventid'] = $eventid;
            $data['eventInfo'] = $eventInfo;
        $subcategory = new Subcategory();
        $categoryName = "";
        $data['paginationpageid'] = '1';


        if($req['categoryOfBusiness']==null){
            $categoriesSelected = array();
        }else{
            $categoriesSelected = $req['categoryOfBusiness'];
        }
        if($req['subcategoryOfBusiness']==null){
            $subcategoriesSelected = array();
        }else{
            $subcategoriesSelected = $req['subcategoryOfBusiness'];
        }
        $categoryFilterString = "user_professional_info.business_category !=''";

        $prefix = DB::getTablePrefix();

        $countryString = "";
        $data['countryselected'] = "";
        if($req['country']!=NULL && $req['country']!=""){
            $countryString = " AND ". $prefix."users.country='".$req['country']."' " ;
            // echo $countryString;exit();
            $data['countryselected'] = $req['country'];
        }


        $cityString = "";
        $data['cityselected'] = "";
        if($req['city']!=NULL && $req['city']!=""){
            $cityString = " AND ". $prefix."users.city='".$req['city']."' " ;
            $data['cityselected'] = $req['city'];
        }

        $loggedInUser = loggedInUserId();

        $categoryAppendStr = "";

        if(count($categoriesSelected)!=0){
            $explodedCategories = "'".implode("','", $categoriesSelected)."'";
            $categoryAppendStr = " AND ".$prefix."user_professional_info.business_category IN (".$explodedCategories.") ";
        }

        $subcategoryAppendStr = "";

        if(count($subcategoriesSelected)!=0){
            $explodedCategories = "'".implode("','", $subcategoriesSelected)."'";
            $subcategoryAppendStr = " AND ".$prefix."user_professional_info.subcategory IN (".$explodedCategories.") ";
        }

        /* 
        [
            ['users.userid','!=',$loggedInUser],
            ['user_professional_info.business_category',' IN ',"('3','4')"]
        ]
        */
        //$usersList = array();
        $usersList = DB::table('users')->whereRaw($prefix.'users.user_type=\'Participant\''." AND ".$prefix."users.user_type='Participant'  AND ".$prefix."users.profile_completion_status='Complete' AND ".$prefix."users.userid !='".$loggedInUser."' ".$categoryAppendStr. " ".$subcategoryAppendStr." ".$countryString." ".$cityString)
        ->join('virtual_event_participants',
            function($join) use($eventid)
                            {
                                $join->on('virtual_event_participants.userid', '=', 'users.userid');
                                $join->where('virtual_event_participants.event_id','=', $eventid);
                            }

    )
        ->leftJoin('user_professional_info', 'users.userid', '=', 'user_professional_info.userid')->orderBy('users.id','asc')->paginate(10)->toArray();

        $data['pageId']  = 'searchprofiles';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        $citiesOfCountry = array();

        $subcategory = new Subcategory();
        $categoryName = "";
        $data['userslist'] = $usersList;
        $data['inputcategories'] = $categoriesSelected;
        $data['inputsubcategories'] = $subcategoriesSelected;
        $paginationPageId = "1";
        if($req->input('page') != NULL){
                $paginationPageId = $req->input('page');
        }

        $data['paginationpageid'] = $paginationPageId;

        return view("profilessearchandview.searchprofiles",$data);

        }
    }
    function searchresults(Request $req){
    	if($req['categoryOfBusiness']==null){
    		$categoriesSelected = array();
    	}else{
    		$categoriesSelected = $req['categoryOfBusiness'];
    	}
    	if($req['subcategoryOfBusiness']==null){
    		$subcategoriesSelected = array();
    	}else{
    		$subcategoriesSelected = $req['subcategoryOfBusiness'];
    	}
		$categoryFilterString = "user_professional_info.business_category !=''";
		$loggedInUser = loggedInUserId();

    	$prefix = DB::getTablePrefix();

		$categoryAppendStr = "";


        $countryString = "";
        $data['countryselected'] = "";
        if($req['country']!=NULL && $req['country']!=""){
            $countryString = " AND ". $prefix."users.country='".$req['country']."' " ;
            // echo $countryString;exit();
            $data['countryselected'] = $req['country'];
        }


        $cityString = "";
        $data['cityselected'] = "";
        if($req['city']!=NULL && $req['city']!=""){
            $cityString = " AND ". $prefix."users.city='".$req['city']."' " ;
            $data['cityselected'] = $req['city'];
        }

		if(count($categoriesSelected)!=0){
			$explodedCategories = "'".implode("','", $categoriesSelected)."'";
			$categoryAppendStr = " AND ".$prefix."user_professional_info.business_category IN (".$explodedCategories.") ";
		}

		$subcategoryAppendStr = "";

		if(count($subcategoriesSelected)!=0){
			$explodedCategories = "'".implode("','", $subcategoriesSelected)."'";
			$subcategoryAppendStr = " AND ".$prefix."user_professional_info.subcategory IN (".$explodedCategories.") ";
		}

		/* 
		[
    		['users.userid','!=',$loggedInUser],
    		['user_professional_info.business_category',' IN ',"('3','4')"]
    	]
    	*/
    	$usersList = DB::table('users')->whereRaw($prefix.'users.user_type=\'Participant\''." AND ".$prefix."users.user_type='Participant'  AND ".$prefix."users.profile_completion_status='Complete' AND ".$prefix."users.userid !='".$loggedInUser."' ".$categoryAppendStr. " ".$subcategoryAppendStr." ".$countryString." ".$cityString)->leftJoin('user_professional_info', 'users.userid', '=', 'user_professional_info.userid')->orderBy('users.id','asc')->paginate(10)->toArray();

    	$data['pagetitle'] = "Search Profiles";
        $data['pageId']  = 'searchprofiles';
        $country = new Category();
        $data['categories'] = $country->orderBy('category', 'asc')->get()->toArray();
        $citiesOfCountry = array();

        $subcategory = new Subcategory();
        $categoryName = "";
        $data['userslist'] = $usersList;
        $data['inputcategories'] = $categoriesSelected;
        $data['inputsubcategories'] = $subcategoriesSelected;
        $paginationPageId = "1";
        if($req->input('page') != NULL){
        		$paginationPageId = $req->input('page');
        }

        $data['paginationpageid'] = $paginationPageId;

        return view("profilessearchandview.searchprofiles",$data);


    	// echo "HIi";

    }
}
