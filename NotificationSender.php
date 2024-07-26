<?php

namespace NW\WebService\References\Operations\Notification;

use NW\WebService\References\Operations\MessagesClient;
use NW\WebService\References\Operations\NotificationManager;

final class NotificationSender
{
    public function sendEmployeeNotifications(array $emails, string $emailFrom, array $templateData, int $resellerId): void
    {
        foreach ($emails as $email) {
            MessagesClient::sendMessage([
                [
                    'emailFrom' => $emailFrom,
                    'emailTo' => $email,
                    'subject' => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                    'message' => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                ],
            ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS);
        }
    }

    public function sendClientEmailNotification(string $emailTo, string $emailFrom, array $templateData, int $resellerId, int $newStatus): void
    {
        MessagesClient::sendMessage([
            [
                'emailFrom' => $emailFrom,
                'emailTo' => $emailTo,
                'subject' => __('complaintClientEmailSubject', $templateData, $resellerId),
                'message' => __('complaintClientEmailBody', $templateData, $resellerId),
            ],
        ], $resellerId, $newStatus, NotificationEvents::CHANGE_RETURN_STATUS);
    }

    public function sendClientSmsNotification(string $mobile, int $resellerId, int $clientId, array $templateData, int $newStatus, array &$result): void
    {
        $error = '';
        $res = NotificationManager::send($resellerId, $clientId, NotificationEvents::CHANGE_RETURN_STATUS, $newStatus, $templateData, $error);
        if ($res) {
            $result['notificationClientBySms']['isSent'] = true;
        }
        if (!empty($error)) {
            $result['notificationClientBySms']['message'] = $error;
        }
    }
}
