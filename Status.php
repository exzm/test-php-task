<?php

namespace NW\WebService\References\Operations\Notification;

enum Status: int
{
    case COMPLETED = 0;
    case PENDING = 1;
    case REJECTED = 2;

    public function getName(): string
    {
        return match ($this) {
            self::COMPLETED => 'Completed',
            self::PENDING => 'Pending',
            self::REJECTED => 'Rejected',
        };
    }
}
