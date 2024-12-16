<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';

    /**
     * Mengambil semua value enum sebagai array.
     *
     * @return array
     */
    public static function list(): array
    {
        return [
            self::PRESENT->value,
            self::ABSENT->value,
            self::LATE->value,
        ];
    }
}
