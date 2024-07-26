<?php

namespace NW\WebService\References\Operations\Notification;

class Contractor
{
    const int TYPE_CUSTOMER = 0;
    public int $id;
    public string $type;
    public string $name;

    protected function __construct(int $id)
    {
        return ['id' => $id]; //fake data
    }

    public static function getById(int $id): ?self
    {
        return new self($id); // fakes the getById method
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}
