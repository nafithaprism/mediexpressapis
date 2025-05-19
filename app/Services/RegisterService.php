<?php

namespace App\Services;

use App\Events\RegisterMail;
use Illuminate\Http\Request;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Http\Client\Pool;
use Str;
use DB;
use Redirect;


class RegisterService
{

    private $httpService;
    private $authService;



    public function registerUser($data)
    {

        $check = User::where('email', '=', $data['email'])->count();
        $user = User::where('email', '=', $data['email'])->get();

        if ($check > 0 && $data->is_social) {


            $dd = ['first_name' => $data->first_name, 'email' => $data->email, 'password' => $data->password];
            $check = ['email' => $data->email, 'password' => $data->password];
            $token =  $user->createToken($user->email)->plainTextToken;

            return response()->json($user, 200)->header('x_auth_token', $token)->header('access-control-expose-headers', 'x_auth_token');
        } else {
            $userData['first_name'] = $data['first_name'];
            $userData['last_name'] = $data['last_name'];
            $userData['email'] = $data['email'];
            $userData['password'] = $data['password'];
            $userData['isActive'] = 0;
            $userData['mobile'] = $data['mobile'];
            $fourRandomDigit = rand(1000, 9999);
            $userData['code'] = $fourRandomDigit;
            $userCreate = TempUser::create($userData);
            event(new RegisterMail($userData));
            return json_encode(['message' => 'E-Mail Verification has been sent to your registered account!', 'status' => 200]);
        }
    }

    public function tokenVerify($code)
    {

        $user = TempUser::where('code', $code)->first();

        if ($user) {

            $timeLimit = strtotime($user['created_at']) + 1800;
            if (time() > $timeLimit) {
                $user->delete();
                return response()->json(['error' => 'Token Expired', 400]);
            } else {

                $create = [

                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'password' => bcrypt($user->password),
                    'mobile' => $user->mobile
                ];

                $userCreate =  User::firstOrcreate($create);
                $usersDetail  = $user;
                $user->delete();
                return $usersDetail;
            }
        } else {
            return $user;
        }
    }
}
