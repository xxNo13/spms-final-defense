<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'office' => ['required'],
        ], [
            'name.required' => 'Name cannot be null.',
            'email.required' => 'Email cannot be null.',
            'office.required' => 'Office cannot be null.'
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'faculty_position_id' => $input['faculty_position_id']
        ]);

        $user->offices()->attach($input['office']);
        if (isset($input['account_type'])) {
            $user->account_types()->attach($input['account_type']);
        }
        if (isset($input['institute'])) {
            $user->institutes()->attach($input['institute']);
        }

        session()->flash('message', 'User Successfully Registered!');
        return auth()->user();
    }
}
