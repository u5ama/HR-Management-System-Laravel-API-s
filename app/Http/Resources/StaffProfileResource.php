<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffProfileResource extends JsonResource
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
                'first_name'=>$this->first_name,
                'last_name'=>$this->last_name,

                'email'=>$this->user->email,

                'phone_number'=>$this->phone_number,
                'date_of_birth'=>$this->date_of_birth,
                'home_address'=>$this->home_address,
                'city'=>$this->city,
                'state'=>$this->state,
                'zip_code'=>$this->zip_code,
                'last_4_of_SNN'=>$this->last_4_of_SNN,

                'type_of_worker'=>$this->staff_info->type_of_worker,
                'type_of_employee'=>$this->staff_info->type_of_employee,
                'type_of_contractor'=>$this->staff_info->type_of_contractor,
                'business_name'=>$this->staff_info->business_name,
                'start_date'=>$this->staff_info->start_date,
                'state_working_in'=>$this->staff_info->state_working_in,
                'pay_rate_type'=>$this->staff_info->pay_rate_type,
                'pay_rate_amount'=>$this->staff_info->pay_rate_amount,

                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at,
            ];
    }
}
