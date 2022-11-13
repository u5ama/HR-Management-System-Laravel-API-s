<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffRolesResource;
use App\Models\StaffRoles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff = StaffRoles::where('company_id', $request->company_id)->get();
        $staff = StaffRolesResource::collection($staff);
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
                'role_name' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = new StaffRoles();

            $staff->company_id = $request->company_id;
            $staff->role_name = $request->role_name;
            $staff->save();

            $staff = new StaffRolesResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_roles_store','message'=>$message];
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
        $staff = StaffRoles::where(['id' => $request->id])->first();
        if ($staff) {
            $staff = new StaffRolesResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Role found!',
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
                'company_id' => 'required',
                'role_name' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = StaffRoles::where('id',$id)->first();

            $staff->company_id = $request->company_id;
            $staff->role_name = $request->role_name;
            $staff->save();

            $staff = new StaffRolesResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_roles_update','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse|void
     */
    public function destroy($id)
    {
        try {
            $staff = StaffRoles::findorFail($id);
            if ($staff) {
                $staff->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Role deleted successfully!',
                ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_roles_destroy','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
