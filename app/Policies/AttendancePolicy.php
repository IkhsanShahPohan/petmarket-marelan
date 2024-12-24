<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     * Admin bisa melihat semua list, pegawai hanya bisa melihat miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || isset($user->employee);
    }

    /**
     * Determine whether the user can view the model.
     * Admin bisa melihat semua, pegawai hanya bisa melihat miliknya sendiri.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true; // Admin bisa update semua
        }

        // Pegawai hanya bisa update absensi miliknya
        return isset($user->employee) && $attendance->employee_id == $user->employee->id;
    }

    /**
     * Determine whether the user can create models.
     * Pegawai boleh menambahkan absensi, admin tidak perlu menambahkan.
     */
    public function create(User $user): bool
    {
        return $user->role == 'pegawai'; // Hanya pegawai yang bisa membuat absensi
    }

    /**
     * Determine whether the user can update the model.
     * Admin bisa update semua absensi, pegawai hanya bisa update miliknya sendiri.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        // if ($user->isAdmin()) {
        //     return true; // Admin bisa update semua
        // }

        // Pegawai hanya bisa update absensi miliknya
        return isset($user->employee) && $attendance->employee_id == $user->employee->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Admin bisa delete semua absensi, pegawai tidak bisa delete absensi.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        // if ($user->isAdmin()) {
        //     return true; // Admin bisa update semua
        // }

        // Pegawai hanya bisa update absensi miliknya
        return isset($user->employee) && $attendance->employee_id == $user->employee->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }
}
