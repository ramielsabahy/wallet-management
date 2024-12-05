<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class TransferAmountValidRule implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::whereEmail($this->data['email'])->first();
        if (auth()->user()->wallet->balance < $value) {
            $fail("Your balance isn't enough to transfer.");
        }
        if (!$user){
            $fail("Target User not found.");
        }
        if ($value >= 25 && auth()->user()->wallet->balance + 25 < $value){
            $fail("Your balance isn't enough to transfer.");
        }
        if ($user?->email == auth()->user()->email){
            $fail("You can't transfer to yourself.");
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
