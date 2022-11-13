<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffProfileResource;
use App\Models\Staff;
use App\Models\StaffDetails;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $company_id = $request->company_id;
        $staff = Staff::with('user', 'staffDetails','staffRole')->where('company_id', $company_id)->get();
        $staff = StaffProfileResource::collection($staff);
        return response()->json([
            'success' => true,
            'data' => $staff,
        ],200, ['Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
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
                'company_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'phone_number' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = Staff::create([
                'company_id' => $request->company_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'home_address' => $request->home_address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'assigned_role_id' => $request->assigned_role_id,
            ]);

            User::create([
                'staff_id' => $staff->id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_status' => 'active',
                'user_type' => 'staff',
            ]);

            StaffDetails::create([
                'staff_id' => $staff->id,
                'type_of_worker' => $request->type_of_worker,
                'type_of_employee' => $request->type_of_employee,
                'type_of_contractor' => $request->type_of_contractor,
                'business_name' => $request->business_name,
                'start_date' => $request->start_date,
                'state_working_in' => $request->state_working_in,
                'pay_rate_type' => $request->pay_rate_type,
                'pay_rate_amount' => $request->pay_rate_amount,
            ]);
            $staff = Staff::with('user', 'staffDetails')->where('id', $staff->id)->first();
            $staff = new StaffProfileResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_store','message'=>$message];
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
        $staff = Staff::with('user', 'staffDetails','staffRole')->where(['id' => $request->id])->first();
        if ($staff) {
            $staff = new StaffProfileResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff found!',
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
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|string|email|max:255|unique:users',
                'phone_number' => 'required',
//                'last_4_of_SNN' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = Staff::where('id', $id)->first();

            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->phone_number = $request->phone_number;
            $staff->date_of_birth = $request->date_of_birth;
            $staff->home_address = $request->home_address;
            $staff->city = $request->city;
            $staff->state = $request->state;
            $staff->zip_code = $request->zip_code;
            $staff->assigned_role_id = $request->assigned_role_id;
            $staff->save();


            User::where(['staff_id' => $staff->id])->update([
                'email' => $request->email,
            ]);

            StaffDetails::where(['staff_id' => $staff->id])->update([
                'type_of_worker' => $request->type_of_worker,
                'type_of_employee' => $request->type_of_employee,
                'type_of_contractor' => $request->type_of_contractor,
                'business_name' => $request->business_name,
                'start_date' => $request->start_date,
                'state_working_in' => $request->state_working_in,
                'pay_rate_type' => $request->pay_rate_type,
                'pay_rate_amount' => $request->pay_rate_amount,
            ]);

            $staff = new StaffProfileResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_update','message'=>$message];
            $errors = [$error];
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
            $staff = Staff::findorFail($id);
            if ($staff) {

                User::where(['staff_id' => $staff->id])->delete();
                StaffDetails::where(['staff_id' => $staff->id])->delete();

                $staff->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Staff deleted successfully!',
                ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_destroy','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
