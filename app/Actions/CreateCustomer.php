<?php

namespace App\Actions;

use Stripe\Customer;

class CreateCustomer extends Action
{

    /**
     * Get the validation rules for the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
        ];
    }

    /**
     * Run the action.
     *
     * @param  array<string, mixed>  $data
     * @return Customer
     */
    public function run(array $data): Customer
    {
        $data = $this->validate($data);

        $customer = stripe()->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $customer;
    }
}