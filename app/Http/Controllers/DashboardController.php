<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\ContactUs;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Mail\UserTrackMail;
use Illuminate\Support\Facades\Mail;
class DashboardController extends Controller
{
    public function userListing()
    {
        $user = User::where('user_type', 'user')->get();
        return parent::returnData($user, 200);
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->delete();
        return parent::returnStatus($user, 200);
    }

    public function formList()
    {
        $ContactUs = ContactUs::get();
        return parent::returnData($ContactUs, 200);
    }

    public function deleteForm($id)
    {
        $ContactUs = ContactUs::where('id', $id)->delete();
        return parent::returnStatus($ContactUs, 200);
    }

    public function subscriberList()
    {
        $subscriber = Subscriber::get();
        return parent::returnData($subscriber, 200);
    }

    public function deleteSubscriber($id)
    {
        $subscriber = Subscriber::where('id', $id)->delete();
        return parent::returnStatus($subscriber, 200);
    }


    public function cards()
    {
        $data['orders'] = Order::count();
        $data['sales'] = Order::sum('total_amount');
        $data['users'] = User::count(); 
        $data['product'] = Product::count();

        return parent::returnData($data, 200);
    }


    public function allOrders()
    {
        $order['order'] = Order::with([
            'orderDetails.product' => function ($q) {
                $q->select('id', 'name', 'featured_img', 'route');
            }
        ])->with('country')->with('user', function ($q) {
            $q->select('id', 'first_name', 'last_name', 'email');
        })->get();
        $order['pending_orders'] = Order::where('status', 'PENDING')->count();
        $order['confirmed_orders'] = Order::where('status', 'ORDERPLACED')->count();
        $order['total_amount'] = Order::where('status', 'ORDERPLACED')->sum('total_amount');
        $order['total_orders'] = Order::count();

        return parent::returnData($order, 200);
    }

    public function orderDetail($id)
    {

        $order = Order::where('id', $id)->with([
            'user' => function ($q) {
                $q->select('id', 'first_name', 'email');
            }
        ])->with('billingAddress')->with('orderDetails.products', function ($q) {
            $q->select('id', 'name', 'featured_img', 'route');
        })->select(
                'id',
                'user_id',

                'order_number',
                'payment_type',

                'discounted_amount',
                'shipping_charges',
                'total_amount',
                'status',
                'billing_address_id'
            )->first();
        return parent::returnData($order, 200);
    }


    public function orderCard()
    {

        $data['pending_orders'] = Order::where('status', 'PENDING')->count();
        $data['confirmed_orders'] = Order::where('status', 'ORDERPLACED')->count();
        $data['total_amount'] = Order::where('status', 'ORDERPLACED')->sum('total_amount');
        $data['total_orders'] = Order::count();

        return $data;
    }




    public function orderFilter(Request $request)
    {

        $results = Order::when(!empty($request->startDate), function ($q) {
            $start = Carbon::createFromFormat('Y-m-d', request('startDate'))->startOfDay();
            $q->where('created_at', '>=', $start);
        })
            ->when(!empty($request->endDate), function ($q) {
                $end = Carbon::createFromFormat('Y-m-d', request('endDate'))->endOfDay();
                $q->where('created_at', '<=', $end);
            })
            ->when(!empty($request->status), function ($q) {
                $q->where('status', request('status'));
            })
            ->when(count($request->all()) === 0, function ($q) {
                return ['data' => 'No record found.', 'status' => 404];
            })->with('user')->get();

        if ($results->count() > 0) {
            return response()->json(['data' => $results]);
        } else {
            return ['data' => 'No record found.', 'status' => 404];
        }
    }


    public function sendTracking(Request $request)
    {
        $data = $request->all();
        $update = [
            'tracking_number' => $data['tracking_number'],
            'logistics_partner_name' => $data['logistics_partner_name'],
            'logistics_partner_link' => $data['logistics_partner_link'],
        ];

        // Perform the update
        $affectedRows = Order::where('order_number', $data['order_number'])->update($update);

        // Fetch the updated order
        $order = Order::where('order_number', $data['order_number'])->first();
        $user = $order->user;
        $address = $order->billingAddress;
        $userData = [
            'tracking_number' => $data['tracking_number'],
            'logistics_partner_name' => $data['logistics_partner_name'],
            'logistics_partner_link' => $data['logistics_partner_link'],
            'user' => $user,
            'address' => $address
        ];
        $userMail = $user['email'];
        Mail::to($user['email'])->send(new UserTrackMail($userData));

        // Return response
        return response()->json(['data' => 'Action submitted successfull!', 'status' => 200]);

    }

}