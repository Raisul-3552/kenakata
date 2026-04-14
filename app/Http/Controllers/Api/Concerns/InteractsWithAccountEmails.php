<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Support\Facades\DB;

trait InteractsWithAccountEmails
{
    protected function accountEmailDefinitions(): array
    {
        return [
            [
                'table' => '[Admin]',
                'primaryKey' => 'AdminID',
                'role' => 'admin',
                'token' => 'admin-token',
            ],
            [
                'table' => 'Employee',
                'primaryKey' => 'EmployeeID',
                'role' => 'employee',
                'token' => 'employee-token',
            ],
            [
                'table' => 'Customer',
                'primaryKey' => 'CustomerID',
                'role' => 'customer',
                'token' => 'customer-token',
            ],
            [
                'table' => 'DeliveryMan',
                'primaryKey' => 'DelManID',
                'role' => 'deliveryman',
                'token' => 'deliveryman-token',
            ],
        ];
    }

    protected function findAccountByEmail(string $email): ?array
    {
        foreach ($this->accountEmailDefinitions() as $definition) {
            // MSSQL Query: Search for user by email in each table
            $user = DB::selectOne("
                SELECT * FROM " . $definition['table'] . " WHERE Email = ?
            ", [$email]);

            if ($user) {
                return [
                    'user' => $user,
                    'role' => $definition['role'],
                    'token' => $definition['token'],
                    'table' => $definition['table'],
                    'primaryKey' => $definition['primaryKey'],
                ];
            }
        }

        return null;
    }

    protected function emailExistsAcrossAccounts(string $email, ?array $ignore = null): bool
    {
        foreach ($this->accountEmailDefinitions() as $definition) {
            // MSSQL Query: Check if email exists in this table
            $query = "SELECT COUNT(*) as cnt FROM " . $definition['table'] . " WHERE Email = ?";
            $params = [$email];

            if ($ignore && ($ignore['table'] ?? null) === $definition['table'] && isset($ignore['id'])) {
                $query .= " AND " . $definition['primaryKey'] . " != ?";
                $params[] = $ignore['id'];
            }

            $result = DB::selectOne($query, $params);
            
            if ($result && $result->cnt > 0) {
                return true;
            }
        }

        return false;
    }
}
