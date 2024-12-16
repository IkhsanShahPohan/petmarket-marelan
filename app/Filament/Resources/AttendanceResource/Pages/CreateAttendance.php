<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Redirect;


class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
    
}


