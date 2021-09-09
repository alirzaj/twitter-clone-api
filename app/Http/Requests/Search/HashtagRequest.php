<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class HashtagRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hashtag' => ['required', 'string', 'regex:/#\S+/']
        ];
    }
}
