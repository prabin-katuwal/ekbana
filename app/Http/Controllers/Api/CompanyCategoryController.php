<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCategoryResource;
use App\Http\Resources\CompanyResource;
use App\Models\CompanyCategory;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        if ($keyword) {
            $companycategory = CompanyCategory::where('title', 'like', '%' . $keyword . '%')->get();
            if (count($companycategory) > 0) {
                return response()->json($companycategory);
            }
            return response()->json(['message' => 'No Result Found'], 200);
        }
        $companycategories = CompanyCategory::paginate(10);
        $companycategory = CompanyCategoryResource::collection($companycategories);
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
        // validation
        $validator = Validator::make($request->all(), [
            "title" => "required"
        ]);
        //

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
                $companycategory = new CompanyCategory();
                $companycategory->title = $request->title;
                $companycategory->save();
                DB::commit();
                return response()->json(['messagestatus' => true, "message" => "Data Save Successfully"], 201);
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
    public function show($id)
    {
        $companycategory = CompanyCategory::with('companies')->find($id);
        if ($companycategory) {
            return new CompanyCategoryResource($companycategory);
        }
        return response()->json(['message' => 'Record Not Found'], 404);
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
        $companycategory = CompanyCategory::find($id);
        if (!empty($companycategory)) {
            // validation
            $validator = Validator::make($request->all(), [
                "title" => "required"
            ]);
            //
            if ($validator->fails()) {
                return response()->json(
                    [
                        "messagestatus" => false,
                        "message" => $validator->errors()->first(),
                    ],
                    422
                );
            }
            DB::beginTransaction();
            try {
                $companycategory->title = $request->title;
                $companycategory->save();
                DB::commit();
                return response()->json(["messagestatus" => true, "message" => "Data updated Successfully"], 201);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json(['messagestatus' => false, 'message' => $e->getMessage()], 501);
            }
        }
        return response()->json(['message' => 'Record Not Found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $companycategory = CompanyCategory::find($id);
        if ($companycategory) {
            $companycategory->delete();
            return response()->json(['message' => 'Data Deleted Successfully']);
        }
        return response()->json(['message' => 'Record Not Found'], 404);
    }
}
