<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Http;


class FormHandlerController extends Controller
{
  public function store(Request $request)
{
    try {
        // Validate incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        // Save the data into the ContactUs table
        $contact = ContactUs::create($validatedData);

        // Prepare data for Zoho API
        $zohoData = [
            'data' => [
                [
                    'First_Name' => $validatedData['first_name'],
                    'Last_Name' => $validatedData['last_name'],
                    'Email' => $validatedData['email'],
                    'Phone' => $validatedData['phone'],
                    'Message' => $validatedData['message'],
                ]
            ]
        ];

        // Set the Zoho API URL and key (same approach as in register)
        $zohoApiUrl = "https://www.zohoapis.com/crm/v2/functions/contact_us/actions/execute?auth_type=apikey&zapikey=1003.f5ca8087dfb09f924ac85da52cd845b3.4cda840fcba6c112c266788b6ff46004";

        // Send data to Zoho API using POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($zohoApiUrl, $zohoData);

        // Check response from Zoho API
        if ($response->successful()) {
            return response()->json([
                'message' => 'Data stored successfully and sent to Zoho.',
                'data' => $contact,
                'zoho_response' => $response->json(),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data stored successfully, but failed to send to Zoho.',
                'data' => $contact,
                'zoho_response' => $response->json(),
            ], 500);
        }

    } catch (\Error $exception) {
        return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
    }
}
}