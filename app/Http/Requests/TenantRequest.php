<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;

class TenantRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $routename = Route::currentRouteName();

        if ($routename == 'tenant.store') {
            return [
                'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:tenants,email'],
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&]/',
                ],
                'password_confirmation' => 'required',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255|unique:tenants,name|regex:/^[A-Za-z0-9\s]+$/',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'required|string|max:500',
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).',

            'company_name.required' => 'The company name field is required.',
            'company_name.regex' => 'The company name can only contain letters and numbers. Special characters are not allowed.',
            'company_name.unique' => 'The company name has already been taken.',
        ];
    }
}
