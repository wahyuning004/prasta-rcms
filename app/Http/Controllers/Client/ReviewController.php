<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan atau perbarui penilaian (rating 1-5 & ulasan) untuk pesanan yang sudah selesai.
     */
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak dapat memberikan ulasan pada pesanan milik pelanggan lain.');

        if ($order->status !== 'completed') {
            return back()->with('error', 'Penilaian hanya dapat diberikan untuk pesanan yang sudah berstatus Selesai.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rating.required' => 'Pilih rating bintang 1 sampai 5.',
            'rating.integer' => 'Rating harus berupa angka 1 sampai 5.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Tuliskan ulasan atau komentar Anda mengenai layanan.',
            'comment.min' => 'Ulasan minimal 5 karakter.',
            'comment.max' => 'Ulasan maksimal 1000 karakter.',
        ]);

        $serviceId = $order->service_id ?: $order->items()->value('service_id');
        $existing = Review::where('order_id', $order->id)->first();

        Review::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => Auth::id(),
                'service_id' => $serviceId,
                'rating' => (int) $validated['rating'],
                'comment' => trim($validated['comment']),
                'status' => $existing && $existing->status === 'rejected' ? 'pending' : 'published',
            ]
        );

        $msg = $existing
            ? 'Penilaian dan ulasan Anda berhasil diperbarui. Terima kasih!'
            : 'Terima kasih! Penilaian dan ulasan Anda berhasil dikirim.';

        return redirect()->route('klien.orders.show', $order->id)
            ->with('success', $msg);
    }
}
