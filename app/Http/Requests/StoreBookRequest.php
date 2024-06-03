<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
        // return the validation rules for the book requirement check it doesn't break the rules to store 
        // the book requirement.
        return [
            'title' => 'required|max:255|',
            'author' => 'required|max:255',
            'published_date' => 'required|date',
            'genre' => 'required|max:255',
            'price' => 'required|numeric',
        ];
    }
}
