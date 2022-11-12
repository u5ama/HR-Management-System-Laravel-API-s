<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StaffNotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'id'=>$this->id,
                'note_type'=>$this->note_type,
                'note_date'=>$this->note_date,
                'note_description'=>$this->note_description,
                'note_file'=> $this->note_file ? Storage::url($this->note_file) : null,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at,
            ];
    }
}
