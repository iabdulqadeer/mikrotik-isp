<?php

namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemUserRequest extends FormRequest {
  public function authorize(): bool { return $this->user()->can('update', $this->route('user')); }
  public function rules(): array {
    $id = $this->route('user')->id;
    return [
      'role_id'     => ['required','string'],
      'first_name' => ['required','string','max:100'],
      'last_name'  => ['required','string','max:100'],
      'username' => ['required','alpha_dash','min:3','max:50',"unique:users,username,$id"],
      'phone'    => ['required','string','max:32'],
      'email'    => ["required","email","max:255","unique:users,email,$id"],
      'internet_profile_id' => ['nullable','exists:internet_profiles,id'],
    ];
  }
}
