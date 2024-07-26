<?php

namespace NW\WebService\References\Operations\Notification;

use Exception;

final class Validator
{
    /**
     * @throws Exception
     */
    public function validateResellerId(int $resellerId): void
    {
        if (empty($resellerId)) {
            throw new Exception('Empty resellerId', 400);
        }
    }

    /**
     * @throws Exception
     */
    public function validateNotificationType(int $notificationType): void
    {
        if (empty($notificationType)) {
            throw new Exception('Empty notificationType', 400);
        }
    }

    /**
     * @throws Exception
     */
    public function validateTemplateData(array $templateData): void
    {
        foreach ($templateData as $key => $value) {
            if (empty($value)) {
                throw new Exception("Template Data ({$key}) is empty!", 500);
            }
        }
    }
}
