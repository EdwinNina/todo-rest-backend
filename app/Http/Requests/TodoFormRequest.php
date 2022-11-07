<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoFormRequest extends FormRequest
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
            'title' => 'required|max:60',
            'file' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024'
        ];
    }
}
