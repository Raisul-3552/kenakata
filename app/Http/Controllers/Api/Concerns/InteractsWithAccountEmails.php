<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\DeliveryMan;
use App\Models\Employee;

trait InteractsWithAccountEmails
{
    protected function accountEmailDefinitions(): array
    {
        return [
            [
                'model' => Admin::class,
                'role' => 'admin',
                'token' => 'admin-token',
                'primaryKey' => 'AdminID',
            ],
            [
                'model' => Employee::class,
                'role' => 'employee',
                'token' => 'employee-token',
                'primaryKey' => 'EmployeeID',
            ],
            [
                'model' => Customer::class,
                'role' => 'customer',
                'token' => 'customer-token',
                'primaryKey' => 'CustomerID',
            ],
            [
                'model' => DeliveryMan::class,
                'role' => 'deliveryman',
                'token' => 'deliveryman-token',
                'primaryKey' => 'DelManID',
            ],
        ];
    }

    protected function findAccountByEmail(string $email): ?array
    {
        foreach ($this->accountEmailDefinitions() as $definition) {
            $user = $definition['model']::where('Email', $email)->first();

            if ($user) {
                return [
                    'user' => $user,
                    'role' => $definition['role'],
                    'token' => $definition['token'],
                    'model' => $definition['model'],
                ];
            }
        }

        return null;
    }

    protected function emailExistsAcrossAccounts(string $email, ?array $ignore = null): bool
    {
        foreach ($this->accountEmailDefinitions() as $definition) {
            $query = $definition['model']::where('Email', $email);

            if ($ignore && ($ignore['model'] ?? null) === $definition['model'] && isset($ignore['id'])) {
                $query->where($definition['primaryKey'], '!=', $ignore['id']);
            }

            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }
}
