<?php
namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Category;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;  // Pastikan DB sudah di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;  // Jangan lupa untuk menambahkan import Request

class Kasir extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Kasir';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.kasir'; // View yang akan digunakan

    public $products;
    public $categories;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show the page for users with the 'kasir' role
        return auth()->user()->role === 'kasir';
    }

    // Method untuk mengambil data dan mengirimkannya ke view
    public function mount()
    {
        // Mengambil data produk dan kategori
        $this->products = Product::with('category')->get();
        $this->categories = Category::all();
        if (Auth::user()->role !== 'kasir') {
            abort(403, 'You are not authorized to access this page.');
        }

    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $cart = $validated['cart'];
        $notes = $validated['notes'] ?? '';

        // Membuat invoice date dan status
        $invoiceDate = now();
        $status = 'Paid'; // Or 'Cancelled' based on your logic

        DB::beginTransaction();

        try {
            // Insert data cart ke database (misalnya, melalui stored procedure atau query manual)
            DB::statement('CALL InsertCartToInvoice(?, ?, ?, ?)', [
                $invoiceDate,
                $status,
                $notes,
                json_encode($cart), // Mengubah cart ke format JSON
            ]);

            // Commit transaksi jika berhasil
            DB::commit();

            // Mengirimkan respons sukses
            return response()->json(['message' => 'Checkout successful']);
        } catch (\Exception $e) {
            // Rollback transaksi jika gagal
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return response()->json(['message' => 'Checkout failed'], 500);
        }
    }

}
