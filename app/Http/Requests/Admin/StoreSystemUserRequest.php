<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreSystemUserRequest extends FormRequest {
  public function authorize(): bool { return $this->user()->can('create', \App\Models\User::class); }
  public function rules(): array {
    return [
      'role_id'     => ['required','string'],
      'first_name' => ['required','string','max:100'],
      'last_name'  => ['required','string','max:100'],
      'username' => ['required','alpha_dash','min:3','max:50','unique:users,username'],
      'phone'    => ['required','string','max:32'],
      'email'    => ['required','email','max:255','unique:users,email'],
      'password' => ['required','string','min:8'],
      'internet_profile_id' => ['nullable','exists:internet_profiles,id'],
      'send_sms' => ['nullable','boolean'],
    ];
  }
}

