<?php
namespace App\Http\Controllers;

use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $service) {}

    // GET /api/discounts
    public function index()
    {
        return response()->json(
            Discount::latest()->paginate(20)
        );
    }

    // GET /api/discounts/{discount}
    public function show(Discount $discount)
    {
        return response()->json($discount);
    }

    // POST /api/discounts
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => ['required','string','max:64','unique:discounts,code'],
            'type'          => ['required', Rule::in(['percentage','flat'])],
            'value'         => ['required','numeric','min:0'],
            'valid_till'    => ['nullable','date'],
            'active'        => ['boolean'],
              'influencer' => ['nullable','string','max:255'],   // 👈 string
             'usage_count'   => ['sometimes','integer','min:0'],          // ← new (optional input)
    'is_eligible_for_commission' => ['sometimes','boolean'],     // ← new
        ]);

        // guard: percentage must be <= 100
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            $data['value'] = 100;
        }

        $discount = $this->service->create($data);
        return response()->json($discount, 201);
    }

    // PUT /api/discounts/{discount}
    public function update(Request $request, Discount $discount)
    {
        $data = $request->validate([
            'code'          => ['sometimes','string','max:64', Rule::unique('discounts','code')->ignore($discount->id)],
            'type'          => ['sometimes', Rule::in(['percentage','flat'])],
            'value'         => ['sometimes','numeric','min:0'],
            'valid_till'    => ['nullable','date'],
            'active'        => ['boolean'],
            'influencer' => ['nullable','string','max:255'],   // 👈 string
             'usage_count'   => ['sometimes','integer','min:0'],          // ← new (optional input)
    'is_eligible_for_commission' => ['sometimes','boolean'],     // ← new
        ]);

        if (($data['type'] ?? $discount->type) === 'percentage' && isset($data['value']) && $data['value'] > 100) {
            $data['value'] = 100;
        }

        $updated = $this->service->update($discount, $data);
        return response()->json($updated);
    }

    // DELETE /api/discounts/{discount}
    public function destroy(Discount $discount)
    {
        $this->service->delete($discount);
        return response()->noContent();
    }

    // POST /api/discounts/apply  (public/guest accessible)
    // body: { "code": "ABC10", "amount": 199.99 }
    public function apply(Request $request)
    {
        $data = $request->validate([
            'code'   => ['required','string','max:64'],
            'amount' => ['required','numeric','min:0.01'],
        ]);

        $result = $this->service->apply($data['code'], (float)$data['amount']);
        return response()->json($result);
    }
}
