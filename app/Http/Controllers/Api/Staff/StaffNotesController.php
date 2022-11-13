<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffNotesResource;
use App\Models\StaffNotes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StaffNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff = StaffNotes::where('staff_id', $request->staff_id)->get();
        $staff = StaffNotesResource::collection($staff);
        return response()->json([
            'success' => true,
            'data' => $staff,
        ],200, ['Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
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
                'note_type' => 'required',
                'note_date' => 'required',
                'note_description' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            $path = '';
            if ($file = $request->file('note_file')) {
                $path = $file->store('public/staff/notes');
                $name = $file->getClientOriginalName();
            }

            $staff = new StaffNotes();

            $staff->staff_id = $request->staff_id;
            $staff->note_type = $request->note_type;
            $staff->note_date = $request->note_date;
            $staff->note_description = $request->note_description;
            $staff->note_file = $path;
            $staff->save();

            $staff = new StaffNotesResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff Notes created successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_notes_store','message'=>$message];
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
        $staff = StaffNotes::where(['id' => $request->id])->first();
        if ($staff) {
            $staff = new StaffNotesResource($staff);
            return response()->json([
                'success' => true,
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Staff Note found!',
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
                'note_type' => 'required',
                'note_date' => 'required',
                'note_description' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $staff = StaffNotes::where('dtaff_id',$id)->first();
            $path = '';
            if ($file = $request->file('note_file')) {
                $path = $file->store('public/staff/notes');
                $name = $file->getClientOriginalName();
            }else if(!empty($staff->note_file)){
                $path = $staff->note_file;
            }

            $staff->staff_id = $request->staff_id;
            $staff->note_type = $request->note_type;
            $staff->note_date = $request->note_date;
            $staff->note_description = $request->note_description;
            $staff->note_file = $path;
            $staff->save();

            $staff = new StaffNotesResource($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff Notes updated successfully!',
                'data' => $staff,
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_notes_update','message'=>$message];
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
            $staff = StaffNotes::findorFail($id);
            if ($staff) {
                $staff->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Staff Note deleted successfully!',
                ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'staff_notes_destroy','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
