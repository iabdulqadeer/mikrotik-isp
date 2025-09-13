<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest {
  public function authorize(): bool { return $this->user()->can('update', $this->route('user')); }
  public function rules(): array {
    return ['password' => ['required','string','min:8','confirmed']];
  }
}