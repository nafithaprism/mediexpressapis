<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\user\LoginRequest;
use App\Http\Requests\user\ResetRequest;
use App\Http\Requests\user\RegisterRequest;
use App\Http\Requests\user\ForgetRequest;
use App\Http\Requests;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\PasswordReset;
use App\Mail\ForgetMail;
use App\Services\ForgetService;
use App\Services\RegisterService;
use App\Services\UserService;
use DateTime;
use Redirect;
use Validator;
use Session;
use Hash;
use Auth;
use Mail;
use DB;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{

    ####Register#####
 public function register(RegisterRequest $request)
{
    try {
        // Get all request data
        $data = $request->all();
        
        // Prepare data for Zoho API
        $zohoData = [
            'data' => [
                [
                    'First_Name' => $data['first_name'],
                    'Last_Name'  => $data['last_name'],
                    'Email'      => $data['email'],
                    'Mobile'     => $data['mobile'],
                ]
            ]
        ];

        // Set the Zoho API URL and key
        $url = "https://www.zohoapis.com/crm/v2/functions/register_contacts/actions/execute?auth_type=apikey&zapikey=1003.f5ca8087dfb09f924ac85da52cd845b3.4cda840fcba6c112c266788b6ff46004";

        // Send the data to Zoho API using a POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $zohoData);

        // Check if the response is successful
        if ($response->successful()) {
            // Optionally, you can save the user to your database here, if needed
            $user = (new RegisterService())->registerUser($data);

            return response()->json([
                'message' => 'User registered successfully, and data sent to Zoho.',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'error' => 'Failed to register user with Zoho',
                'details' => $response->body()
            ], 400);
        }

    } catch (\Error $exception) {
        return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
    }
}

    // public function register(RegisterRequest $request)
    // {

    //     try {

    //         $data = $request->all();
    //         $user  = (new RegisterService())->registerUser($data);
    //         return $user;
    //     } catch (\Error $exception) {
    //         return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
    //     }
    // }

    public function emailVerify(Request $request)
    {
        try {
            $data = $request->all();
            $user =  (new RegisterService())->tokenVerify($data);
            if (!empty($user)) {

                if (!Auth::attempt(['email' => $user['email'], 'password' => $user['password']])) {

                    return response()->json(['error' => 'Credentials does not match']);
                }

                $token = auth()->user()->createToken('API_Token')->plainTextToken;

                return response()->json(['success' => 'Logged in successfully', 200])->header('x_auth_token', $token)->header('access-control-expose-headers', 'x_auth_token');
            } else {

                return response()->json(['error' => 'Invalid Token', 200]);
            }
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    ####End Register#####


    public function login(LoginRequest $request)
    {

        try {

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => $request->user_type])) {

                return response()->json(['error' => 'Credentials does not match']);
            }

            $token = auth()->user()->createToken('API_Token')->plainTextToken;

            return response()->json(['success' => 'Logged in successfully' ,'user' => auth()->user() , 200])->header('x_auth_token', $token)->header('access-control-expose-headers', 'x_auth_token');
        } catch (BadMethodCallException $e) {

            return response()->json(['error' => 'Email/Password is invalid.', 404]);
        }
    }

    public function me()
    {

        return auth()->user();
    }


    public function updateProfile(Request $request)
    {
        try {

            $data = $request->all();
            $user =  (new UserService())->update($data);

            if ($user) {
                return response()->json('Updated Successfully', 200);
            }
        } catch (BadMethodCallException $e) {

            return response()->json('Email/Password is invalid.', 404);
        }
    }


    public function changePassword(ResetRequest  $request)
    {

        try {

            $data = $request->all();
            $user =  (new UserService())->changePassword($data);

            if ($user) {
                return response()->json('Updated Successfully', 200);
            }
        } catch (BadMethodCallException $e) {

            return response()->json('Email/Password is invalid.', 404);
        }
    }


    #####Forget Password#####

    public function forgetPassword(ForgetRequest $request)
    {

        try {

            $forget = ForgetService::sendToken($request->all());
            if ($forget) {

                return  response()->json('We have Emailed your password reset link!', 200);
            }
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    public function resetPassword($token)
    {

        try {
            $forget = ForgetService::resetPassword($token);
            return $forget;
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    public function submitResetPassword(Request $request)
    {

        try {
            $data = $request->all();
            $forget = ForgetService::submitResetPassword($data);
            return Redirect::away($forget);
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    #####End Forgot Password######


    public function trackOrder($id)
    {

        $status = Order::where('order_number', $id)->first('status');
        return $status;
    }

    public function logout()
    {

        dd(auth()->user());
        // $user->tokens()->where('id', $tokenId)->delete();

        if (!auth()->user()->tokens()->delete()) return response()->json('Server Error.', 400);

        return response()->json('You are logged out successfully', 200);
    }


    public function deleteUser($id)
    {
        $status = User::where('id', $id)->delete();
        return response()->json('User Deleted!', 200);
    }


    public function addAddress(Request $request){
        
        $data = $request->all();

        $address = [
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'country' => $data['country'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'address_line1' => $data['address_line1'],
            'address_line2' => $data['address_line2'],
            'address_type' =>  $data['address_type'],

        ];
        $create = Address::create($address);
        return parent::returnData($create, 200);

    }


    public function userAddress($id){

        $address = Address::where('user_id',$id)->get();
        return parent::returnData($address, 200);

    }
    
    
    public function deleteAddress( $id){

        $address = Address::where('id',$id)->delete();
        return parent::returnStatus($address, 200);
       
    }
    
    
    public function viewAddress($id){
        
         $address = Address::where('id',$id)->first();
        return parent::returnData($address, 200);
        
    }
    
    public function updateAddress(Request $request){
           
        $data = $request->all();

        $address = [
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'country' => $data['country'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'address_line1' => $data['address_line1'],
            'address_line2' => $data['address_line2'],
            'address_type' =>  $data['address_type'],

        ];
        
         $address = Address::where('id',$data['id'])->update($address);
        return parent::returnStatus($address, 200);
        
    }

    
  
}
