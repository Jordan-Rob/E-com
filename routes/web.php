<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::match(['get', 'post'], '/admin', 'AdminController@login');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Home page 
Route::get('/', 'IndexController@index');

//Category/listing page
Route::get('/products/{url}', 'ProductsController@products');

//product detail page
Route::get('/product/{id}','ProductsController@product');

//Add to cart page
Route::match(['get', 'post'], '/add-cart', 'ProductsController@addToCart');

//cart page
Route::match(['get', 'post'], '/cart', 'ProductsController@cart');

//Delete product from cart
Route::get('/cart/delete-product/{id}', 'ProductsController@deleteCartProduct');

//Update product quantity in cart
Route::get('/cart/update-quantity/{id}/{quantity}', 'ProductsController@updateCartQuantity');

//get productattribute price
Route::get('/get-product-price','ProductsController@getProductPrice');

//Apply coupon code
Route::post('/cart/apply-coupon', 'ProductsController@applyCoupon');

//users login/register page 
Route::get('/login-register', 'UsersController@userLoginRegister');

//Users Register form submit
Route::post('/user-register', 'UsersController@register');

//Users Login
Route::post('/user-login', 'UsersController@login');

//Users logout
Route::get('/user-logout', 'UsersController@logout');

//routes after user login
Route::group(['middleware' => ['frontlogin']], function(){
    //User Account
    Route::match(['get', 'post'], '/account', 'UsersController@account');
});


//check if user already exists
Route::match(['get', 'post'], '/check-email', 'UsersController@checkEmail');


//Admin routes
Route::group(['middleware' => ['auth']], function(){
    Route::get('/admin/dashboard', 'AdminController@dashboard');
    Route::get('/admin/settings', 'AdminController@settings');
    Route::get('/admin/check-pwd', 'AdminController@chkPassword');
    Route::match(['get', 'post'],'/admin/update-pwd', 'AdminController@updatePassword');

    //categories routes
    Route::match(['get', 'post'],'/admin/add-category', 'CategoryController@addCategory');
    Route::match(['get', 'post'],'/admin/edit-category/{id}', 'CategoryController@editCategory');
    Route::match(['get', 'post'],'/admin/delete-category/{id}', 'CategoryController@deleteCategory');
    Route::get('/admin/view-categories', 'CategoryController@viewCategories');
    
    //products routes
    Route::match(['get', 'post'],'/admin/add-product', 'ProductsController@addProduct');
    Route::match(['get', 'post'],'/admin/edit-product/{id}', 'ProductsController@editProduct');
    Route::get('/admin/view-products', 'ProductsController@viewProducts');
    Route::get('/admin/delete-product-image/{id}', 'ProductsController@deleteProductImage');
    Route::get('/admin/delete-alt-image/{id}', 'ProductsController@deleteAltImage');
    Route::get('/admin/delete-product/{id}', 'ProductsController@deleteProduct');

    //products attributes routes
    Route::match(['get', 'post'],'/admin/add-attributes/{id}', 'ProductsController@addAttributes');
    Route::match(['get', 'post'],'/admin/add-images/{id}', 'ProductsController@addImages');
    Route::get('/admin/delete-attribute/{id}', 'ProductsController@deleteAttribute');

    //Coupon Routes
    Route::match(['get', 'post'],'/admin/add-coupon', 'CouponsController@addCoupon');
    Route::get('/admin/view-coupons', 'CouponsController@viewCoupons');
    Route::match(['get', 'post'],'/admin/edit-coupon/{id}', 'CouponsController@editCoupon');
    Route::get('/admin/delete-coupon/{id}', 'CouponsController@deleteCoupon');

    //Banner Routes
    Route::match(['get', 'post'],'/admin/add-banner', 'BannersController@addBanner');
    Route::get('/admin/view-banners', 'BannersController@viewBanners');
    Route::match(['get', 'post'],'/admin/edit-banner/{id}', 'BannersController@editBanner');
    Route::get('/admin/delete-banner/{id}', 'BannersController@deleteBanner');
});

Route::get('/logout', 'AdminController@logout');
