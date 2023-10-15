<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MatchOldPassword implements Rule
{
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function passes($attribute, $value)
    {
        return Hash::check($value, User::find($this->id)->password);
    }


    public function message()
    {
        return 'The :attribute is not match with current password.';
    }
}
