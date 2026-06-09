<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class testcontroller extends Controller
{
    //
    function index(){

    	$dataSend = array("name" => "Srinath");
    	$months = array("Jan","Feb","March","April","May","June","July","August","September","October","November","December");
    	$dataSend['months']=$months;
    	return view("testviews/datapassing",$dataSend);
    }
    function homepage(){

    	$dataSend = array("name" => "Srinath");
    	$months = array("Jan","Feb","March","April","May","June","July","August","September","October","November","December");
    	$dataSend['months']=$months;
    	return view("accountpages/home",$dataSend);	
    }
    function stringscheck($id){
    	$info = "Hi , my N@ME is Venkata SREENATH";
    	/*echo "<h1>".$info."</h1>";
    	$info = Str::replaceFirst("Hi","Hello",$info);
    	echo "<h1>".$info."</h1>";
    	$info = Str::ucfirst($info);
    	echo "<h1>".$info."</h1>";
    	$info = Str::camel($info);*/
    	$info = Str::of($info)->replaceFirst("Hi","Hello",$info)->camel($info);
    	echo "<h1>".$info."</h1>";
    	return $id;
    }
}
