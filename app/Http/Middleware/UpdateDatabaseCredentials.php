<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateDatabaseCredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek jika role pengguna adalah 'admin'
            if ($user->role === 'admin') {
                // Update kredensial database untuk admin
                Config::set('database.connections.mysql.username', 'admin');
                Config::set('database.connections.mysql.password', 'adminpassword');

                // Reset koneksi database
                DB::disconnect('mysql');
                DB::reconnect('mysql');
            } elseif ($user->role === 'kasir') {
                // Update kredensial database untuk kasir
                Config::set('database.connections.mysql.username', 'kasir');
                Config::set('database.connections.mysql.password', 'kasirpassword');

                // Reset koneksi database
                DB::disconnect('mysql');
                DB::reconnect('mysql');
            } elseif ($user->role === 'pegawai') {
                // Update kredensial database untuk pegawai
                Config::set('database.connections.mysql.username', 'pegawai');
                Config::set('database.connections.mysql.password', 'pegawaipassword');

                // Reset koneksi database
                DB::disconnect('mysql');
                DB::reconnect('mysql');
            } else {
                // Jika role tidak sesuai
                throw new Exception("Role pengguna tidak valid atau tidak memiliki akses.");
            }

        }

        return $next($request);
    }
}
