<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Banner;

class IndexController extends Controller
{
    public function index(){
        /*in Ascending order (default)
        $productsAll = Product::get();*/

        //in descending order 
        //$productsAll = Product::orderBy('id','DESC')->get();

        //in Random order
        $productsAll = Product::inRandomOrder()->where('status',1)->get();

        //get all categories and sub-categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();
        /*$categories = json_decode(json_encode($categories));
        echo "<pre>"; print_r($categories);die;*/

        $banners = Banner::where('status', 1)->get();

        return view('index')->with(compact('productsAll', 'categories', 'banners'));
    }
}
