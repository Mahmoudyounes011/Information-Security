<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'parking_spot_id' => 'required|exists:parking_spots,id',
            'reservation_time' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'parking_spot_id.required' => 'Parking spot ID is required.',
            'parking_spot_id.exists' => 'The selected parking spot does not exist.',
            'reservation_time.required' => 'Reservation time is required.',
            'reservation_time.date' => 'The reservation time must be a valid date.',
        ];
    }
}
