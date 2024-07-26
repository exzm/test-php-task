<?php

namespace NW\WebService\References\Operations\Notification;

use Exception;
use NW\WebService\References\Operations\Contractor;
use NW\WebService\References\Operations\Employee;
use NW\WebService\References\Operations\Seller;
use NW\WebService\References\Operations\MessagesClient;
use NW\WebService\References\Operations\NotificationManager;

class TsReturnOperation extends ReferencesOperation
{
    public const int TYPE_NEW = 1;
    public const int TYPE_CHANGE = 2;

    private Validator $validator;
    private DataFetcher $dataFetcher;
    private NotificationSender $notificationSender;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->dataFetcher = new DataFetcher();
        $this->notificationSender = new NotificationSender();
    }

    /**
     * @throws Exception
     */
    public function doOperation(): array
    {
        $data = (array)$this->getRequest('data');
        $resellerId = (int)$data['resellerId'];
        $notificationType = (int)$data['notificationType'];

        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ];

        try {
            $this->validator->validateResellerId($resellerId);
            $this->validator->validateNotificationType($notificationType);

            $client = $this->dataFetcher->getClient((int)$data['clientId'], $resellerId);
            $creator = $this->dataFetcher->getEmployee((int)$data['creatorId'], 'Creator');
            $expert = $this->dataFetcher->getEmployee((int)$data['expertId'], 'Expert');

            $clientFullName = $client->getFullName();
            if (empty($clientFullName)) {
                $clientFullName = $client->name;
            }

            $differences = $this->getDifferencesMessage($notificationType, $data, $resellerId);

            $templateData = [
                'COMPLAINT_ID' => (int)$data['complaintId'],
                'COMPLAINT_NUMBER' => (string)$data['complaintNumber'],
                'CREATOR_ID' => (int)$data['creatorId'],
                'CREATOR_NAME' => $creator->getFullName(),
                'EXPERT_ID' => (int)$data['expertId'],
                'EXPERT_NAME' => $expert->getFullName(),
                'CLIENT_ID' => (int)$data['clientId'],
                'CLIENT_NAME' => $clientFullName,
                'CONSUMPTION_ID' => (int)$data['consumptionId'],
                'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'],
                'AGREEMENT_NUMBER' => (string)$data['agreementNumber'],
                'DATE' => (string)$data['date'],
                'DIFFERENCES' => $differences,
            ];

            $this->validator->validateTemplateData($templateData);

            $emailFrom = (new Emails())->getResellerEmailFrom($resellerId);
            $emails = (new Emails())->getEmailsByPermit($resellerId, 'tsGoodsReturn');

            if (!empty($emailFrom) && count($emails) > 0) {
                $this->notificationSender->sendEmployeeNotifications($emails, $emailFrom, $templateData, $resellerId);
                $result['notificationEmployeeByEmail'] = true;
            }

            if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) {
                if (!empty($emailFrom) && !empty($client->email)) {
                    $this->notificationSender->sendClientEmailNotification($client->email, $emailFrom, $templateData, $resellerId, (int)$data['differences']['to']);
                    $result['notificationClientByEmail'] = true;
                }

                if (!empty($client->mobile)) {
                    $this->notificationSender->sendClientSmsNotification($client->mobile, $resellerId, $client->id, $templateData, (int)$data['differences']['to'], $result);
                }
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    private function getDifferencesMessage(int $notificationType, array $data, int $resellerId): string
    {
        if ($notificationType === self::TYPE_NEW) {
            return __('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($data['differences'])) {
            return __('PositionStatusHasChanged', [
                'FROM' => Status::from((int)$data['differences']['from'])->name,
                'TO' => Status::from((int)$data['differences']['to'])->name,
            ], $resellerId);
        }
        return '';
    }
}

final class Emails
{
    public function getResellerEmailFrom(int $resellerId): string
    {
        return 'contractor@example.com';
    }

    public function getEmailsByPermit(int $resellerId, string $event): array
    {
        return ['someemail@example.com', 'someemail2@example.com'];
    }
}
