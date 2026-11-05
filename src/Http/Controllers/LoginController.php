<?php
namespace Jyim\LaravelPluginMarket\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Jyim\LaravelPluginMarket\Exceptions\ApiRequestException;
use Jyim\LaravelPluginMarket\Models\MarketUser;
use Jyim\LaravelPluginMarket\Http\Requests\User\LoginRequest;

class LoginController extends Controller
{
    /**
     * @param  LoginRequest  $request
     * @return Response
     */
    public function login(LoginRequest $request): Response
    {
        $user = MarketUser::query()->where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw new ApiRequestException('账号密码错误！');
        }
        return response(["token" => $user->createToken($user->email)->plainTextToken]);
    }
}