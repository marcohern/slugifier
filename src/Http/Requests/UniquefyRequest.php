<?php

namespace Marcohern\Slugifier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniquefyRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
        'source' => 'required|max:128',
        'entity' => 'max:64'
    ];
  }
}