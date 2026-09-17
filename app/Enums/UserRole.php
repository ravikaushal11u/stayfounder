<?php

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case PROVIDER = 'provider';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Student',
            self::PROVIDER => 'Accommodation Provider',
            self::ADMIN => 'Administrator',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::STUDENT => 'student.dashboard',
            self::PROVIDER => 'provider.dashboard',
            self::ADMIN => 'admin.dashboard',
        };
    }
}
