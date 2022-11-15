<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffDocumentsResource;
use App\Models\StaffDocuments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StaffDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff = StaffDocuments::where('staff_id', $request->staff_id)->get();
        if ($staff){
            $staff = StaffDocumentsResource::collection($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff Document found!',
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
                'document_title' => 'required',
                'document_file' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $path = '';
            if ($file = $request->file('document_file')) {
                $path = $file->store('public/staff/documents');
                $name = $file->getClientOriginalName();
            }
            $staff = new StaffDocuments();

            $staff->staff_id = $request->staff_id;
            $staff->document_title = $request->document_title;
            $staff->document_file = $path;
            $staff->save();

            $staff = new StaffDocumentsResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff Document created successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_document_store','message'=>$message];
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
        $staff = StaffDocuments::where(['id' => $request->id])->first();
        if ($staff) {
            $staff = new StaffDocumentsResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff Document found!',
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
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
                'staff_id' => 'required',
                'document_title' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = StaffDocuments::where('id', $id)->first();

            if ($file = $request->file('document_file')) {
                $path = $file->store('public/staff/documents');
                $name = $file->getClientOriginalName();
            }else if(!empty($staff->document_file)){
                $path = $staff->document_file;
            }

            $staff->staff_id = $request->staff_id;
            $staff->document_title = $request->document_title;
            $staff->document_file = $path;
            $staff->save();

            $staff = new StaffDocumentsResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff Document updated successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_document_update','message'=>$message];
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
            $staff = StaffDocuments::findorFail($id);
            if ($staff) {
                $staff->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Staff Document deleted successfully!',
                ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_document_destroy','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
