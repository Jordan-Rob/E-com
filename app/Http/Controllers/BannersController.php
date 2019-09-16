<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Banner;
use Image;

class BannersController extends Controller
{
    public function addBanner(Request $request){
        if($request->ismethod('post')){
            $data = $request->all();
            //echo "<pre>";print_r($data);die;
            $banner = new Banner;
            $banner->title=$data['title'];
            $banner->link=$data['link'];
           

            //image upload
            if($request->hasFile('image')){
               $image_tmp=Input::file('image');
               if($image_tmp->isValid()){
                   $extension = $image_tmp->getClientOriginalExtension();
                   $filename = rand(111,99999).'.'.$extension;
                   $banner_path = 'images/frontend_img/banners/'.$filename;
                   Image::make($image_tmp)->resize(1140,340)->save($banner_path);
                   //Store image name in products table
                   $banner->image=$filename;

               }
            }
            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $banner->status = $status;
            $banner->save();
            //return redirect()->back()->with('flash_message_success', 'Product added Successfully');
            return redirect()->back()->with('flash_message_success', 'Banner added Successfully');
        }
        return view('admin.banners.add_banner');
    }

    public function viewBanners(){
        $banners = Banner::get();
        return view('admin.banners.view_banners')->with(compact('banners'));
    }

    public function editBanner(Request $request, $id=null){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>";print_r($data);die;
            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            if(empty($data['title'])){
                $data['title']='';
            }

            if(empty($data['link'])){
                $data['link'] ='';
            }

            //image upload
            if($request->hasFile('image')){
                $image_tmp=Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $banner_path = 'images/frontend_img/banners/'.$filename;
                    Image::make($image_tmp)->resize(1140,340)->save($banner_path);
                    //Store image name in products table 
                }
             }else if(!empty($data['current_image'])){
                $filename = $data['current_image'];
            }else{
                $filename = '';
            }

            Banner::where('id', $id)->update(['status'=>$status, 'title'=>$data['title'], 'link'=>$data['link'],
            'image'=>$filename ]);
            return redirect()->back()->with('flash_message_success', 'Banner has been updated!');

        }
        $bannerDetails = Banner::where('id', $id)->first();
        return view('admin.banners.edit_banner')->with(compact('bannerDetails'));
    }

    public function deleteBanner($id=null){
        Banner::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Banner deleted successfully!');
    }
}
