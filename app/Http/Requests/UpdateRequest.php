<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
	public function rules()
	{
		return [
			'name' => 'string', 'max:255',
			'password' => 'confirmed','min:5 ',
			'age' => 'integer',
			'profile_image' => 'nullable','image','mimes:jpeg,png,jpg,gif,svg','max:1024',
		];
	}

	public function messages()
	{
		return [
			'name' => 'Enter the valid name',
			'password' => 'Enter the valid password',
			'age' => 'Enter valid age',
		];
	}
}
