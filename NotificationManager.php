<?php

namespace NW\WebService\References\Operations\Notification;

final class NotificationManager
{
    public static function send(int $resellerId, int $clientId, string $event, int $newStatus, array $templateData, string &$error): bool
    {
        return true;
    }
}
