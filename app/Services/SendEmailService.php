<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use App\Models\OrderDetail;
use App\Models\ProductPriceVariation;


class SendEmailService
{

    public function orderPlace($billing, $order, $orderDetail)
    {
        $data['order'] = Order::with('user')->where('id', $order['id'])->first(['id', 'order_number', 'currency', 'created_at', 'user_id', 'billing_address_id', 'payment_type', 'sub_total', 'discounted_amount', 'total_amount', 'shipping_charges']);

        $data['address'] = Address::where('id', $data['order']['billing_address_id'])->first(['country', 'state', 'city', 'address_line1', 'address_line2', 'postal_code', 'mobile']);

        $orderDetail = OrderDetail::where('order_id', $data['order']['id'])->get();

        foreach ($orderDetail as $key => $product) {

            $data['product'][$key] = Product::where('id', $product['product_id'])->first(['id', 'name', 'featured_img']);
            $data['product'][$key]['variation'] = ProductPriceVariation::where('id', $product['product_price_variation'])->select('pack_of', 'actual_price')->first();
        }

        return $data;
    }

}