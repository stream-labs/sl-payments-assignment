<?php

namespace App\Actions;
use Illuminate\Support\Facades\Validator;
abstract class Action
{
    public function rules(): array {
        return [];
    }

    public function validator(): Validator
    {
        return app(Validator::class);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Validate the given data against the action's rules.
     *
     * @param array $data
     * @throws ValidationException
     * @return array
     */
    public function validate(array $data): array
    {
        return Validator::make($data, $this->rules())->validate();
    }

}
