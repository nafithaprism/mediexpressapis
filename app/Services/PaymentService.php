<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Services\BookingService;
use App\Services\SendEmailService;

use App\Models\Order;

class PaymentService
{
    private $token;
    public $httpService;

    public function __construct()
    {
        $this->httpService = new HttpService('payment');
        $this->token = config('networks.Sandbox.API_Key');
    }

    public function generateToken()
    {


        $client = new Client(['base_uri' => 'https://api-gateway.sandbox.ngenius-payments.com']);
        $response = $client->post(
            '/identity/auth/access-token/',
            [
                'headers' => [
                    'Content-Type' => 'application/vnd.ni-identity.v1+json',
                    'Authorization' => 'Basic YjI3Mzk4NjItODBlZC00ZWNhLTk2NTQtNWFiOWY1NDkxYzk2OmFlOTZiMjdmLWE3YmMtNDViNC1iZjExLTE1MGJiZDRhZDU4Mg==',
                ]
            ]
        );
        return json_decode($response->getBody(), true);
    }

    public function makePayment($data)
    {
        $token = $this->generateToken();

        $client = $this->httpService->withHeaders(
            [
                'Authorization' => "Bearer " . $token['access_token'],
                'Content-Type' => 'application/vnd.ni-payment.v2+json',
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ]

        )->post(
            'transactions/outlets/817c6665-857d-4495-98c4-999223414f28/orders',
            $data
        );


        return $client->json();
    }

    public function capturePayment($ref)
    {
        $token = $this->generateToken();
        $client = $this->httpService->withHeaders(
            [
                'Authorization' => "Bearer " . $token['access_token'],
                'Content-Type' => 'application/vnd.ni-payment.v2+json',
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ]

        )->get(
            'transactions/outlets/817c6665-857d-4495-98c4-999223414f28/orders/' . $ref
        );

        $response =  $client->json();
        $mobile = Order::where('reference_id', $response['reference'])->first();

        if ($response['_embedded']['payment'][0]['state'] == "PURCHASED") {
            $successUpdateOrder = (new OrderService())->orderSuccess($response);
            if ($mobile['isMobile'] == 1) {
                $response['response'] = 'Success';
                $response['mobile'] =  1;
                return $response;
            } else {
                $response['response'] = 'https://royal-spirit.prismcloudhosting.com/checkout?status=success';
                $response['mobile'] =  0;
                return $response;
            }
        } elseif ($response['_embedded']['payment'][0]['state'] == "CANCELLED ") {
            if ($mobile['isMobile'] == 1) {
                $response['response'] = 'Cancel';
                $response['mobile'] =  1;
            } else {
                $failureUpdateBooking = (new OrderService())->orderCancel($response);
                $response['response'] = 'https://royal-spirit.prismcloudhosting.com/checkout?status=failed';
                $response['mobile'] =  0;
                return $response;
            }
        } elseif ($response['_embedded']['payment'][0]['state'] == "FAILED") {

            if ($mobile['isMobile'] == 1) {
                $response['response'] = 'Fail';
                $response['mobile'] =  1;
            } else {
                $failureUpdateBooking = (new OrderService())->orderFailure($response);
                $response['response'] = 'https://royal-spirit.prismcloudhosting.com/checkout?status=failed';
                $response['mobile'] =  0;
                return $response;
            }
        } else {

            return response()->json('Server Error');
        }
    }
}
