<?php

namespace App\Http\Traits;
use App\Employee;
use Illuminate\Support\Facades\Request;

trait UploadImageTrait {
    public function uploadimage($request,$getimage,$filepath) {

        if( $request->hasFile( $getimage ) ) {
           $image=$request->image;
           $image_new_name=time().$image->getClientOriginalName();
           $image->move($filepath,$image_new_name);
        }

        return null;

    }
}
