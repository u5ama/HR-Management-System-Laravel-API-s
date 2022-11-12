<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileResource extends JsonResource
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
                'id'=> $this->id,
                'full_name'=> $this->name,
                'email'=> $this->email,
                'user_type'=> $this->user_type,
                'user_status'=> $this->user_status,
                'phone_number'=> $this->phone_number,
                'company_name'=> $this->company_name,
                'company_location_zip_code'=> $this->company_location_zip_code,
                'how_did_you_hear'=> $this->how_did_you_hear,
                'industry_type'=> $this->industry_type,
                'token'=> $this->token,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at,
            ];
    }
}
