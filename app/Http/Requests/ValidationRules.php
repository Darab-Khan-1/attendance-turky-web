<?php

namespace App\Http\Requests;


class ValidationRules
{
    public function employeeSignupAuthenticationValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'avatar' => 'required',
        ];
    }
    
    public function employeeLoginAuthenticationValidationRules(): array
    {
        return [
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ];
    }
    
    
    public function employeeProfileUpdateValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
          
        ];
    }
    
    public function changePasswordValidationRules(): array
    {
        return [
            'old_password' => 'required',
            'new_password' => 'required',
        ];
    }
}
