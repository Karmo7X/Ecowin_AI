<?php

namespace App\Enums;

enum DonationStatusEnum: string
{
    case PENDING = "pending";
    case COMPLETED = "completed";
    case CANCELLED = "cancelled";
}
