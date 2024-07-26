<?php

namespace NW\WebService\References\Operations\Notification;

use Exception;
use NW\WebService\References\Operations\Contractor;
use NW\WebService\References\Operations\Seller;
use NW\WebService\References\Operations\Employee;

final class DataFetcher
{
    /**
     * @throws Exception
     */
    public function getSeller(int $resellerId): Seller
    {
        $reseller = Seller::getById($resellerId);
        if ($reseller === null) {
            throw new Exception('Seller not found!', 400);
        }
        return $reseller;
    }

    /**
     * @throws Exception
     */
    public function getClient(int $clientId, int $resellerId): Contractor
    {
        $client = Contractor::getById($clientId);
        if ($client === null || $client->type !== Contractor::TYPE_CUSTOMER || $client->Seller->id !== $resellerId) {
            throw new Exception('Client not found!', 400);
        }
        return $client;
    }

    /**
     * @throws Exception
     */
    public function getEmployee(int $employeeId, string $role): Employee
    {
        $employee = Employee::getById($employeeId);
        if ($employee === null) {
            throw new Exception("{$role} not found!", 400);
        }
        return $employee;
    }
}
