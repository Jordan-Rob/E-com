<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Auth;
use Session;
use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use DB;


class ProductsController extends Controller
{
    public function addProduct(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>";print_r($data);die;
            if(empty($data['category_id'])){
                return redirect()->back()->with('flash_message_error', 'Under Category is missing!');
            }
            $product = new Product;
            $product->category_id=$data['category_id'];
            $product->product_name=$data['product_name'];
            $product->product_code=$data['product_code'];
            $product->product_color=$data['product_color'];
            $product->description=$data['description'];
            $product->price=$data['price'];

            //image upload
            if($request->hasFile('image')){
               $image_tmp=Input::file('image');
               if($image_tmp->isValid()){
                   $extension = $image_tmp->getClientOriginalExtension();
                   $filename = rand(111,99999).'.'.$extension;
                   $large_image_path = 'images/backend_img/products/large/'.$filename;
                   $medium_image_path = 'images/backend_img/products/medium/'.$filename;
                   $small_image_path = 'images/backend_img/products/small/'.$filename;
                   //Resize image
                   Image::make($image_tmp)->save($large_image_path);
                   Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                   Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                   //Store image name in products table
                   $product->image=$filename;

               }
            }
            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $product->status = $status;
            $product->save();
            //return redirect()->back()->with('flash_message_success', 'Product added Successfully');
            return redirect('/admin/view-products')->with('flash_message_success', 'Product added Successfully');
        }

        //categories dropdown start
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option selected disabled>Select</option>";
        foreach($categories as $cat){
            $categories_dropdown .="<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                $categories_dropdown .="<option value='".$sub_cat->id."'>&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        //categories dropdown ends

        return view('admin.products.add_product')->with(compact('categories_dropdown'));
    }

    public function viewProducts(){
        $products = Product::get();
        $products = json_decode(json_encode($products));
        foreach($products as $key => $val){
            $category_name = Category::where(['id' => $val->category_id])->first();
            $products[$key]->category_name = $category_name->name;
        }
        //echo "<pre>";print_r($products);die;
        return view('admin.products.view_products')->with(compact('products'));
    }

    public function editProduct(Request $request, $id=null){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>";print_r($data);die;

            //image upload
            if($request->hasFile('image')){
                $image_tmp=Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_img/products/large/'.$filename;
                    $medium_image_path = 'images/backend_img/products/medium/'.$filename;
                    $small_image_path = 'images/backend_img/products/small/'.$filename;
                    //Resize image
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                }
             }else{
                 $filename = $data['current_image'];
             }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }


            Product::where(['id'=>$id])->update(['category_id'=>$data['category_id'],
                'product_name'=>$data['product_name'],'product_code'=>$data['product_code'],
                'product_color'=>$data['product_color'], 'description'=>$data['description'], 
                'price'=>$data['price'], 'image'=>$filename, 'status'=>$status]);
            return redirect()->back()->with('flash_message_success', 'Product Updated successfully!');

        }

        $productDetails = Product::where(['id'=>$id])->first();

        //categories dropdown start
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option selected disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$productDetails->category_id){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $categories_dropdown .="<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                if($cat->id==$productDetails->category_id){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                $categories_dropdown .="<option value='".$sub_cat->id."' ".$selected.">&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        //categories dropdown ends

        return view('admin.products.edit_product')->with(compact('productDetails', 'categories_dropdown'));
    }

    public function deleteProductImage($id=null){
        // get product image name
        $productImage = Product::where(['id'=>$id])->first();
        //get image paths 
        $large_image_path = 'images/backend_img/products/large/';
        $medium_image_path = 'images/backend_img/products/medium/';
        $small_image_path = 'images/backend_img/products/small/';

    

        //delete large image if it does not exist in folder
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }

        //delete medium image if it does not exist in folder
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        //delete small image if it does not exist in folder
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }

        //Delete image from products table
        Product::where(['id'=> $id])->update(['image'=>'']);
        return redirect()->back()->with('flash_message_success', 'Product Image deleted successfully!');
    }

    public function deleteAltImage($id=null){
        // get product image name
        $productImage = ProductsImage::where(['id'=>$id])->first();
        //get image paths 
        $large_image_path = 'images/backend_img/products/large/';
        $medium_image_path = 'images/backend_img/products/medium/';
        $small_image_path = 'images/backend_img/products/small/';

    

        //delete large image if it does not exist in folder
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }

        //delete medium image if it does not exist in folder
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        //delete large image if it does not exist in folder
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }

        //Delete image from products table
        ProductsImage::where(['id'=> $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product Alternate Image(s) deleted successfully!');
    }

    public function deleteProduct($id=null){
        Product::where(['id'=> $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product deleted successfully!'); 
    }

    public function addAttributes(Request $request, $id=null){
        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();
        /*$productDetails = json_decode(json_encode($productDetails));*/
        /*echo "<pre>";print_r($productDetails);die;*/


        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>";print_r($data);
            foreach($data['sku'] as $key => $val){
                if(!empty($val)){
                    //Prevent duplicate SKU check
                    $attrCountSKU = ProductsAttribute::where('sku', $val)->count();
                    if($attrCountSKU>0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error',
                         'SKU already exists! Please add another SKU.');
                    }

                    //Prevent duplicate Size check
                    $attrCountSize = ProductsAttribute::where(['product_id'=>$id, 'size'=>$data['size'][$key]])->count();
                    if($attrCountSize>0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error',''.$data['size'][$key].
                         ' Size already exists! Please add a different size.');
                    }
 
                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }
            return redirect('admin/add-attributes/'.$id)->with('flash_message_success', 'Attributes added Succesfully');
        }
        return view('admin.products.add_attributes')->with(compact('productDetails'));
    }

    public function addImages(Request $request, $id=null){
        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();
        
        if($request->isMethod('post')){
            //add images
            $data = $request->all();
            //echo "<pre>";print_r($data);die;
            if($request->hasFile('image')){
                $files = $request->file('image');
                foreach($files as $file){
                   
                    //Upload images after resize
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_img/products/large/'.$fileName;
                    $medium_image_path = 'images/backend_img/products/medium/'.$fileName;
                    $small_image_path = 'images/backend_img/products/small/'.$fileName;
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600,600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    $image->image = $fileName;
                    $image->product_id = $data['product_id'];
                    $image->save();   
                }

                
            }
            return redirect('admin/add-images/'.$id)->with('flash_message_success', 
            'Product images have been added successfully!');
        }
        $productsImages = ProductsImage::where(['product_id'=>$id])->get();

        return view('admin.products.add_images')->with(compact('productDetails', 'productsImages'));
    }

    public function deleteAttribute($id=null){
        ProductsAttribute::where(['id'=> $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Attribute deleted successfully!');
    }

    public function products($url=null){
        //show 404 page if url does not exist
        $countCategory = Category::where(['url'=>$url, 'status'=>1])->count();
        if($countCategory==0){
            abort(404);
        }


        //Get all categories with sub categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();

        $categoryDetails = Category::where(['url' => $url])->first();

        if($categoryDetails->parent_id==0){
            //if url is main category url
            $subCategories = Category::where(['parent_id' => $categoryDetails->id])->get();
            foreach($subCategories as $subCat){
                $cat_ids[]= $subCat->id;
            }
            
            $productsAll = Product::whereIn('category_id',$cat_ids)->where('status',1)->get();
            $productsAll = json_decode(json_encode($productsAll));
            //echo "<pre>";print_r($productsAll);

        }else{
            //if url is sub-category url
            $productsAll = Product::where(['category_id' => $categoryDetails->id])->where('status',1)->get();
        }

        return view('products.listing')->with(compact('categories' ,'categoryDetails', 'productsAll'));
    }

    public function product($id=null){
        //show 404 page if page is disabled
        $productsCount = Product::where(['id'=>$id,'status'=>1])->count();
        if($productsCount == 0){
            abort(404);
        }

        //Get product details
        $productDetails = Product::with('attributes')->where('id',$id)->first();
        $productDetails = json_decode(json_encode($productDetails));
        /*echo "<pre>";print_r($productDetails);die;*/
        $relatedProducts = Product::where('id','!=', $id)->where(['category_id'=>$productDetails->category_id])->get();
        //$relatedProducts = json_decode(json_encode($relatedProducts));

        /*foreach($relatedProducts->chunk(3) as $chunk){
            foreach($chunk as $item){
                echo $item; echo "<bre>";
            }
            echo "<bre><bre><bre>";
        }
        die;*/
        /*echo "<pre>";print_r($relatedProducts);die;*/

        //Get all categories and sub-categories
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();

        //Get all alternate images
        $productAltImages = ProductsImage::where('product_id',$id)->get();
        /*$productAltImages = json_decode(json_encode($productAltImages));
        echo "<pre>";print_r($productAltImages);die;*/

        $total_stock = ProductsAttribute::where('product_id', $id)->sum('stock');

        return view('products.detail')->with(compact('productDetails','categories', 'productAltImages', 'total_stock', 'relatedProducts'));
         
    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        /*echo "<pre>";print_r($data);die;*/
        $proArr = explode("-",$data['idSize']);
        $proAttr = ProductsAttribute::where(['product_id'=>$proArr[0], 'size'=>$proArr[1]])->first();
        echo $proAttr->price;
        echo "#";
        echo $proAttr->stock;
    }

    public function addToCart(Request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $data = $request->all();
        //echo "<pre>";print_r($data);die;
        if(empty($data['user_email'])){
            $data['user_email'] = ' ';
        }
        $session_id = Session::get('session_id');
        if(empty($session_id)){
            $session_id = str_random(40);
            Session::put('session_id', $session_id);
        }
        

        $sizeArr = explode("-",$data['size']);

        $countProducts = DB::table('cart')->where(['product_id'=>$data['product_id'], 'product_color'=>$data['product_color'],
        'size'=>$sizeArr[1], 'session_id'=>$session_id,])->count();

        if($countProducts>0){
            return redirect()->back()->with('flash_message_error', 'Exact product already exists in Cart!');

        }else {
            $getSKU = ProductsAttribute::select('sku')->where(['product_id'=>$data['product_id'], 'size'=>$sizeArr[1]])->first();

            DB::table('cart')->insert(['product_id'=>$data['product_id'], 'product_name'=>$data['product_name'], 
            'product_code'=>$getSKU->sku, 'product_color'=>$data['product_color'], 'price'=>$data['price'],
            'size'=>$sizeArr[1], 'quantity'=>$data['quantity'], 'user_email'=>$data['user_email'], 'session_id'=>$session_id,]);
        }

        return redirect('cart')->with('flash_message_success', 'Product has been added to cart!');
    }

    public function cart(){
        $session_id = Session::get('session_id');
        $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id', $product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;

        }
        //echo "<pre>";print_r($userCart);
        return view('products.cart')->with(compact('userCart'));
    }

    public function deleteCartProduct($id=null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        DB::table('cart')->where('id', $id)->delete();
        return redirect('cart')->with('flash_message_success', 'Product has been deleted from cart!');
    }

    public function updateCartQuantity($id=null,$quantity=null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $getCartDetails = DB::table('cart')->where('id',$id)->first();
        $getAttributeStock = ProductsAttribute::where('sku',$getCartDetails->product_code)->first();
        
        $updated_quantity = $getCartDetails->quantity+$quantity;

        if($getAttributeStock->stock >= $updated_quantity){
            DB::table('cart')->where('id', $id)->increment('quantity',$quantity);
            return redirect('cart')->with('flash_message_success', 'Product has been updated successfully!');

        }else{
            return redirect('cart')->with('flash_message_error', 'Requested product quantity is not available!');
        }

    }

    public function applyCoupon(Request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

        $data = $request->all();
        //echo "<pre>";print_r($data);die;
        $couponCount = Coupon::where('Coupon_Code', $data['Coupon_Code'])->count();
        if($couponCount == 0){
            return redirect()->back()->with('flash_message_error', 'Coupon is not valid!');
        }else{
            //Get coupon Details
            $couponDetails = Coupon::where('Coupon_Code', $data['Coupon_Code'])->first();

            //if coupon is inactive
            if($couponDetails->status==0){
                return redirect()->back()->with('flash_message_error', 'This coupon is inactive!');
            }

            //If coupon is expired
            $expiry_date = $couponDetails->expiry_date;
            $current_date = date('Y-m-d');
            if($expiry_date < $current_date){
                return redirect()->back()->with('flash_message_error', 'This coupon is expired!');
            }

            //Get cart Total Amount
            $session_id = Session::get('session_id');
            $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
            $total_amount = 0;
            foreach($userCart as $item){
               $total_amount = $total_amount + ($item->price * $item->quantity);
            }

            //check if amount is percentage or fixed
            if($couponDetails->amount_type=="Fixed"){
                $couponAmount = $couponDetails->amount;
            }else{
                $couponAmount = $total_amount = ($couponDetails->amount/100);

            }
            
            //Add coupon code and amount in session
            Session::put('CouponAmount', $couponAmount);
            Session::put('CouponCode', $data['Coupon_Code']);
            return redirect()->back()->with('flash_message_success', 'Coupon Code has sucessfully been applied!');

        }

    }
}

