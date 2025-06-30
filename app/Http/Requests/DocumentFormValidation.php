<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidMarkdown;

class DocumentFormValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string','nullable','max:255'],
            'content' => ['string', 'required', 'min:10', 'max:500000',new ValidMarkdown()],
            'excerpt' => ['string','nullable','max:255'],
            'categories_id' => 'array|nullable',
            'categories_id.*' => 'exists:categories,id',
        ];
    }
}
