<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Address;
use App\Models\OrderDetail;
use App\Mail\UserOrderPlaceMail;
use App\Mail\UserOrderCancelMail;
use App\Mail\ClientOrderPlaceMail;
use App\Mail\ClientOrderCancelMail;
use App\Events\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\ProductPriceVariation;
use App\Events\ClientOrderConfirmedMail;

class OrderService
{

    public function order($data)
    {
        $billing = $this->createBillingAddress($data);
        $order = $this->createOrder($data, $billing);
        $orderDetail = $this->createOrderDetail($order, $data);
        #Order Success
        $orderSuccess = $this->orderSuccess($billing, $order, $orderDetail);

        return $order;
    }

    public function createBillingAddress($data)
    {

        $address = [
            'user_id' => $data['user_id'],
            'first_name' => $data['billing_address']['full_name'],
            'email' => $data['billing_address']['email'],
            'mobile' => $data['billing_address']['mobile'],
            'country' => $data['billing_address']['country'],
            'city' => $data['billing_address']['city'],
            'state' => $data['billing_address']['state'],
            'postal_code' => $data['billing_address']['zip_code'],
            'address_line1' => $data['billing_address']['address_line1'],
            'address_line2' => $data['billing_address']['address_line2'],
            'address_type' => "Billing",

        ];

        $create = Address::create($address);

        return $create->id;
    }

    public function createOrder($data, $billingAddressId)
    {


        $Order = [
            'order_number' => "OR" . rand(999, 888888999999) . "MEDI" . rand(999, 888888999999) . "EXPRESS",
            'user_id' => $data['user_id'],
            'billing_address_id' => $billingAddressId,
            'payment_type' => $data['payment_type'],
            'sub_total' => $data['sub_total'],
            'discounted_amount' => isset($data['discounted_amount']) ? $data['discounted_amount'] : "",
            'shipping_charges' => isset($data['shipping_charges']) ? $data['shipping_charges'] : "",
            'total_amount' => $data['total_amount'],
            'currency' => $data['currency'],
            'country_id' => $data['country_id'],
            'status' => "ORDERPLACED",
        ];
        $create = Order::create($Order);
        return $create;
    }

    public function createOrderDetail($order, $data)
    {
        foreach ($data['product'] as $key => $value) {

            $create = [
                'order_id' => $order->id,
                'product_id' => $value['product_id'],
                'product_price_variation' => $value['product_price_variation'],
                'qty' => $value['qty'],
                'country_id' => $data['country_id']
            ];
            $createOrder[$key] = OrderDetail::create($create);
        }
        return true;
    }



    public function orderSuccess($billing, $order, $orderDetail)
    {
        $userData = (new SendEmailService())->orderPlace($billing, $order, $orderDetail);

        $userMail = $userData['order']['user']['email'];
        $clientMail = ['tanuja@prism-me.com'];
        Mail::to($userMail)->send(new UserOrderPlaceMail($userData));
        Mail::to($clientMail)->send(new ClientOrderPlaceMail($userData));
        return true;

    }





    public function productStock($response)
    {

        $order = Order::where()->first();
        $orderDetail = OrderDetail::where('order_id', $order['id'])->get();

        foreach ($orderDetail as $key => $value) {
            $price[$key] = ProductPriceVariation::where('product_id', $value['product_id'])
                ->where('variation_id', $value['product_variation_id'])
                ->where('variation_value_id', $value['product_value_id'])
                ->first();
            $stock[$key] = $price[$key]['stock'] - $value['qty'];
            $updatePrice[$key] = $price[$key]->update(['stock' => $stock[$key]]);
        }
        return true;
    }



}