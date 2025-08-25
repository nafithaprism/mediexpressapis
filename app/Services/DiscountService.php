<?php
namespace App\Services;

use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DiscountService
{
    public function create(array $data): Discount
    {
        return Discount::create($data);
    }

    public function update(Discount $discount, array $data): Discount
    {
        $discount->update($data);
        return $discount->refresh();
    }

    public function delete(Discount $discount): void
    {
        $discount->delete();
    }

    /**
     * Validate and apply a code to a given amount.
     * @return array{discount:number,total:number,code:string,type:string,id:int}
     * @throws ValidationException
     */
    public function apply(string $code, float $amount): array
    {
        $discount = Discount::where('code', strtoupper(trim($code)))
            ->where('active', true)
            ->first();

        if (!$discount) {
            throw ValidationException::withMessages(['code' => 'Invalid or inactive coupon.']);
        }

        if ($discount->valid_till && Carbon::now()->greaterThan($discount->valid_till)) {
            throw ValidationException::withMessages(['code' => 'Coupon has expired.']);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than 0.']);
        }

        // Compute discount
        $discountAmount = 0.0;
        if ($discount->type === 'percentage') {
            $pct = min(max((float)$discount->value, 0), 100);
            $discountAmount = round($amount * ($pct / 100), 2);
        } else { // flat
            $discountAmount = round(max(min((float)$discount->value, $amount), 0), 2);
        }

        $total = round($amount - $discountAmount, 2);

        return [
            'discount' => $discountAmount,
            'total' => $total,
            'code' => $discount->code,
            'type' => $discount->type,
            'id' => $discount->id,
        ];
    }
}
