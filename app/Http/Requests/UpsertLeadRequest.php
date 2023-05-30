<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpsertLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email'],
            'mobile' => ['required', 'regex:/^04\d{2} \d{3} \d{3}$/'],
            'dob' => ['required', 'date_format:Y-m-d'],
            'tax_file_number' => ['required','digits:9'],
            'agreed_terms' => ['required', 'in:Yes,No'],
            'status' => ['required', 'in:Ready For Search,New Prospect']
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Prospect\'s First Name is Required',
            'last_name.required' => 'Prospect\'s Last Name is Required',
            'mobile.required' => 'The mobile number is required.',
            'mobile.regex' => 'Mobile Number must be of the Australian mobile number format (I,e, 04XX XXX XXX)',
            'dob.date_format' => 'DOB must be of the format YYYY-MM-DD, I.e. 1984-12-23',
            'tax_file_number.digits' => 'Tax File Number must have 9 digits with no spaces',
            'agreed_terms.in' => 'Agreed terms must be either Yes or No',
            'status.in' => 'Status must be either Ready For Search or New Prospect'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
