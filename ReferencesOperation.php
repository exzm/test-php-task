<?php

namespace NW\WebService\References\Operations\Notification;

abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    public function getRequest(string $parameterName): mixed
    {
        return $_REQUEST[$parameterName] ?? null;
    }
}
