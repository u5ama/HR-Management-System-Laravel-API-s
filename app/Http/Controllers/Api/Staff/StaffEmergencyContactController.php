<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffEmergencyContactResource;
use App\Models\StaffEmergency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffEmergencyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff = StaffEmergency::where('staff_id', $request->staff_id)->first();
        if ($staff){
            $staff = new StaffEmergencyContactResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff Emergency Contact found!',
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try{
            $validator_array = [
                'staff_id' => 'required',
                'name' => 'required',
                'phone_number' => 'required',
                'relationship' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = new StaffEmergency();

            $staff->staff_id = $request->staff_id;
            $staff->name = $request->name;
            $staff->phone_number = $request->phone_number;
            $staff->relationship = $request->relationship;
            $staff->save();

            $staff = new StaffEmergencyContactResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_emergency_contact_store','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $staff = StaffEmergency::where(['id' => $request->id])->first();
        if ($staff) {
            $staff = new StaffEmergencyContactResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff Emergency Contact found!',
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try{
            $validator_array = [
                'name' => 'required',
                'phone_number' => 'required',
                'relationship' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = StaffEmergency::where('id',$id)->first();

            $staff->name = $request->name;
            $staff->phone_number = $request->phone_number;
            $staff->relationship = $request->relationship;
            $staff->save();

            $staff = new StaffEmergencyContactResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff emergency contact updated successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_emergency_contact_update','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $staff = StaffEmergency::findorFail($id);
            if ($staff) {
                $staff->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Staff emergency contact deleted successfully!',
                ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_emergency_contact_destroy','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
