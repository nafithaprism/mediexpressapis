<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ZohoService;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductPriceVariation;
use App\Services\SendEmailService;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
class OrderController extends Controller
{


    public function index($uId)
    {
        $data = Order::where('user_id', $uId)->with([
            'orderDetails' => function ($q) {
                $q->select('id', 'order_id', 'product_id', 'country_id', 'qty')->with([
                    'products' => function ($q) {
                        $q->select('id', 'name', 'featured_img', 'route')->get();
                    }
                ])->with('country', function ($q) {
                    $q->select('id', 'name', 'route');
                });
            }
        ])->get();

        if (!blank($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 'No Order Found', 404]);
        }
    }

    public function orderDetail($id)
    {

        $Order = Order::where('id', $id)->with('user', function ($q) {
            $q->select('id', 'first_name' ,'last_name', 'email')->first();
        })->with('billingAddress', function ($q) {
            $q->first();
        })->with([
                    'orderDetails' => function ($q) {
                        $q->select('id', 'order_id', 'product_id', 'qty')->with([
                            'products' => function ($q) {
                                $q->select('id', 'name', 'featured_img', 'route')->get();
                            }
                        ]);
                    }
                ])->select('id', 'user_id', 'order_number', 'billing_address_id', 'created_at', 'total_amount', 'status')->first();
        return response()->json($Order);
    }
    public function order(Request $request)
{
    // Log the incoming order data
    Log::info('Order received', ['order_data' => $request->all()]);

    // Send the order to Zoho
    $orderData = $request->all();
    $response = $this->sendOrderToZoho($orderData);

    // Return the response from Zoho or a success message
    return response()->json([
        'message' => $response,
        'status' => 200
    ]);
}

// Method to send the order data to Zoho
private function sendOrderToZoho($orderData)
{
    $client = new Client();

    // Zoho API endpoint
    $zohoApiUrl = 'https://www.zohoapis.com/crm/v2/functions/orders/actions/execute?auth_type=apikey&zapikey=1003.82eab544689f1d0d03e87c6d8e0c723d.97838d4fa11dd6b4738741527c5c6e21';

    // Prepare the payload to be sent to Zoho
    $payload = [
        'order_data' => [
            'user_id' => $orderData['user_id'],
            'product' => $orderData['product'],
            'billing_address' => $orderData['billing_address'],
            'sub_total' => $orderData['sub_total'],
            'payment_type' => $orderData['payment_type'],
            'discounted_amount' => $orderData['discounted_amount'],
            'country_id' => $orderData['country_id'],
            'currency' => $orderData['currency'],
            'shipping_charges' => $orderData['shipping_charges'],
            'total_amount' => $orderData['total_amount']
        ]
    ];

    try {
        $response = $client->post($zohoApiUrl, [
            'json' => $payload
        ]);

        // Capture the full response data for debugging
        $responseData = json_decode($response->getBody()->getContents(), true);
        Log::info('Zoho API Response:', ['response_data' => $responseData]);

        if (isset($responseData['data']) && $responseData['data']['status'] == 'success') {
            return "Order sent to Zoho successfully!";
        } else {
            return "Failed to send order to Zoho. Response: " . json_encode($responseData);
        }
    } catch (\Exception $e) {
        Log::error('Zoho API Request Error', ['error' => $e->getMessage()]);
        return "Error: " . $e->getMessage();
    }
}


    // public function order(Request $request)
    // {

    //     $order = (new OrderService())->order($request->all());
    //      Log::info('Order received', ['order_data' => $request->all()]);
    //     return response()->json(['message' => 'Order has been created successfully', 'status' => 200]);
    // }

    public function orderStock(Request $request)
    {

        $order = Order::where('reference_id', $request['refrence_id'])->first();
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

    public function userOrder($id){

        $order = Order::where('user_id',$id)->select('id','user_id','order_number','currency' ,'sub_total','discounted_amount','total_amount','status')->get();

        if (!blank($order)) {
          return parent::returnData($order, 200);
        } else {
            return response()->json(['status' => 'No Order Found', 404]);
        }
    }





}