<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $username = $input['username'] ?? \Illuminate\Support\Str::slug(\Illuminate\Support\Str::before($input['email'], '@'));
        $base = $username ?: 'user';
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = "{$base}{$counter}";
            $counter++;
        }

        return User::create([
            'name' => $input['name'],
            'username' => $username,
            'role' => 'customer',
            'balance' => 0.00,
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
