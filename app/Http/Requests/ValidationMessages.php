<?php

namespace App\Http\Requests;


class ValidationMessages
{


    public function employeeSignupAuthenticationValidationMessages(): array
    {
        return ([
            'name.required' => "name is required",
            'phone.required' => "phone is required",
            'email.required' => "email is required",
            'email.unique' => "Email already exists",
            'password.required' => "password is required",
            'avatar.required' => "avatar is required",
        ]);
    }
    public function employeeLoginAuthenticationValidationMessages(): array
    {
        return ([
            'email.required' => "email is required",
            'email.exists' => "Email not found",
            'password.required' => "password is required",
        ]);
    }

    public function employeeProfileUpdateValidationMessages(): array
    {
        return ([
            'name.required' => "name is required",
            'phone.required' => "phone is required",
            'email.required' => "email is required",
            
        ]);
    }
    public function changePasswordValidationMessages(): array
    {
        return ([
            'old_password.required' => "old password is required",
            'new_password.required' => "new password is required",
        ]);
    }
}
