<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Http\Traits\UploadImageTrait;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = Company::with('companyCategory')->paginate(10);
        $companycategory=CompanyResource::collection($company);
        return $companycategory;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //    validation
        $validator = Validator::make($request->all(), [
            "title" => "required",
            "status" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    "status" => false,
                    "message" => $validator->errors()->first(),
                ],
                422
            );
        } else {
            DB::beginTransaction();
            try {
                $company = new Company();
                if ($request->has('image')) {
                    $image = $request->image;
                    $image_new_name = time() . $image->getClientOriginalName();
                    $image->move('uploads/company/', $image_new_name);
                    $company->image = 'uploads/company/' . $image_new_name;
                    $company->title = $request->title;
                    $company->category_id = $request->category_id;
                    $company->description = $request->description;
                    $company->status = $request->status;
                    $company->save();
                }
                $company->title = $request->title;
                $company->category_id = $request->category_id;
                $company->description = $request->description;
                $company->status = $request->status;
                $company->save();
                DB::commit();
                return response()->json(['messagestatus' => true, "message" => "Data Save Successfully"],201);
            } catch (Exception $e) {
                return response()->json(['messagestatus' => false, 'message' => $e->getMessage()]);
                DB::rollBack();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $company = Company::with('companycategory')->find($id);
        if ($company) {
            return new CompanyResource($company);
        }
        return response()->json(['message' => 'Record Not Found'],404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $company = Company::find($id);
        if (!empty($company)) {
            //    validation
            $validator = Validator::make($request->all(), [
                "title" => "required",
                "status" => "required"
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        "messagestatus" => false,
                        "message" => $validator->errors()->first(),
                    ],
                    422
                );
            } else {
                DB::beginTransaction();
                try {
                    if ($request->has('image')) {
                        if (file_exists($company->image)) {
                            unlink($company->image);
                        }
                        $image = $request->image;
                        $image_new_name = time() . $image->getClientOriginalName();
                        $image->move('uploads/company/', $image_new_name);
                        $company->image = 'uploads/company/' . $image_new_name;
                        $company->title = $request->title;
                        $company->category_id = $request->category_id;
                        $company->description = $request->description;
                        $company->status = $request->status;
                        $company->save();
                    }
                    $company->title = $request->title;
                    $company->category_id = $request->category_id;
                    $company->description = $request->description;
                    $company->status = $request->status;
                    $company->save();
                    DB::commit();
                    return response()->json(['messagestatus' => true, "message" => "Data Updated Successfully"],201);
                } catch (Exception $e) {
                    return response()->json(['messagestatus' => false, 'message' => $e->getMessage()]);
                    DB::rollBack();
                }
            }
        }
        return response()->json(['message' => 'Record Not Found'],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::find($id);
        if ($company) {
            if (file_exists($company->image)) {
                unlink($company->image);
            }
            $company->delete();
            return response()->json(['messagestatus'=>true,'message' => 'Data Deleted Successfully']);
        }
        return response()->json(['message' => 'Record Not Found'],404);
    }
}
