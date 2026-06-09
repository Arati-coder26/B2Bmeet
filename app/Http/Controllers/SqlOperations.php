<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Product;

class SqlOperations extends Controller
{
    //
    function sqlInsert(){

    	$data = DB::table('users')->get();

    	$data2 = DB::table('users')->where('id','2')->get();
    	
    	return view('testviews.userlist',array('userslist'=>$data));
    }
    function allProducts(){
/*
// Passing the model to the view
$model = Model::find(1);
View::make('view')->withModel($model);*/
    	return Product::all();
    }
    function deleteProducts(){
    	$data = array();
    	$data['productsList'] = $productsList = Product::all();
    	return view("accountpages.productslist",$data);
    }
    function deleteProduct(Request $req){

    	//DB::delete()
    	$product = Product::find($req->deleteproduct);
    	echo $product->delete();
    	$data = array();
    	$data['productsList'] = $productsList = Product::all();
    	return view("accountpages.productslist",$data);	
    }
    function updateProduct(){
    	$data = array();
    	$data['productsList'] = $productsList = Product::all();
    	return view("accountpages.updateProductsList",$data);		
    }
    function updateProductPost(Request $req){
    
    	$productId = $req->selectedproduct;
    	$newProductName = $req->updateproductname;
    	/* procedure 1 */
    	// $updateArray = array('product_name'=>$newProductName);
    	// Product::where('id',$productId)->update($updateArray);

    	/* procedure 2 */
    	$product = Product::find($productId);
    	$product->product_name = $newProductName;
    	$product->save();

    	$data = array();
    	$data['productsList'] = $productsList = Product::all();
    	return view("accountpages.updateProductsList",$data);			
    }
    function productInfo(Product $id = NULL){
    	if($id != NULL){
    	return $id;
    	}else{
    		echo "No Id";
    	}

    }
    function insertProduct(Request $req){

    	$data['input'] = $req->input();
    	$product = new Product;
    	$product->product_name = $req->input('productname');
    	$product->product_id = $req->input('productid');
    	$product->costprice = $req->input('costprice');
    	$product->created_on =  date("Y-m-d H:i:s");
    	$product->description = "";
		$product->save();
    	return view("accountpages.insertproduct",$data);
    }
}
