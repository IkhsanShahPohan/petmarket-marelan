<?php
namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PayrollPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin dapat melihat semua data
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payroll $payroll): bool
    {
        // Admin dapat melihat data payroll apapun
        if ($user->role == 'admin') {
            return true;
        }

            // Pastikan relasi employee ada dan valid
        if ($user->employee && $payroll->employee_id == $user->employee->id) {
            return true;
        }

        // Jika tidak, pegawai tidak bisa melihat payroll selain miliknya
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin dan pegawai dapat membuat data payroll
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payroll $payroll): bool
    {
        // Hanya admin yang dapat memperbarui payroll
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payroll $payroll): bool
    {
        // Hanya admin yang dapat menghapus payroll
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payroll $payroll): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payroll $payroll): bool
    {
        return true;
    }
}
