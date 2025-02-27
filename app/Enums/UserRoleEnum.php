<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case USER = "user";
    case AGENT = "agent";
    case ADMIN = "admin";
}
