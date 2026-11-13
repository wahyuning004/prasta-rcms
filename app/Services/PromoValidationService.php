<?php

namespace App\Services;

use App\Models\Promo;

class PromoValidationService
{
    /**
     * Validasi kode voucher terhadap subtotal transaksi.
     *
     * @return array{valid: bool, message: string, promo: ?Promo, discount: float, final_total: float}
     */
    public function validate(string $code, float $subtotal, bool $lockForUpdate = false): array
    {
        $normalized = strtoupper(trim($code));

        if ($normalized === '') {
            return [
                'valid' => false,
                'message' => 'Kode voucher tidak boleh kosong.',
                'promo' => null,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        $query = Promo::where('code', $normalized);
        if ($lockForUpdate) {
            $query->lockForUpdate();
        }
        $promo = $query->first();

        if (! $promo) {
            return [
                'valid' => false,
                'message' => 'Kode voucher "'.$normalized.'" tidak ditemukan atau tidak valid.',
                'promo' => null,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        if (! $promo->is_active) {
            return [
                'valid' => false,
                'message' => 'Kode voucher "'.$promo->code.'" sedang tidak aktif.',
                'promo' => $promo,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        if ($promo->isExpired()) {
            return [
                'valid' => false,
                'message' => 'Kode voucher "'.$promo->code.'" telah kedaluwarsa pada '.$promo->expired_date->format('d M Y').'.',
                'promo' => $promo,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        if ((int) $promo->quota > 0 && (int) $promo->used_count >= (int) $promo->quota) {
            return [
                'valid' => false,
                'message' => 'Maaf, batas kuota pemakaian voucher "'.$promo->code.'" telah habis.',
                'promo' => $promo,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        $minTransaction = (float) ($promo->min_transaction ?? 0);
        if ($minTransaction > 0 && $subtotal < $minTransaction) {
            return [
                'valid' => false,
                'message' => 'Voucher "'.$promo->code.'" membutuhkan minimal transaksi Rp '.number_format($minTransaction, 0, ',', '.').'.',
                'promo' => $promo,
                'discount' => 0.0,
                'final_total' => $subtotal,
            ];
        }

        $discount = $promo->calculateDiscountFor($subtotal);
        $finalTotal = max(0.0, $subtotal - $discount);

        return [
            'valid' => true,
            'message' => 'Voucher "'.$promo->code.'" berhasil diterapkan! Potongan sebesar Rp '.number_format($discount, 0, ',', '.').'.',
            'promo' => $promo,
            'discount' => $discount,
            'final_total' => $finalTotal,
        ];
    }
}
