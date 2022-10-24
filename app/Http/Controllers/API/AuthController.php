<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Exception;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function authValidate(Request $request)
    {
        try {
            if (!$request->header('Authorization')) throw new Exception('헤더 필수값 누락', 2001);
            if ($request->header('Authorization') != "Bearer ".env('API_KEY')) throw new Exception('API KEY 불일치', 2002);
            if (empty($request->t) || $request->t < (Carbon::now()->getTimestamp()-86400)) throw new Exception('timestamp 유효하지 않음', 2003);
        } catch (\Throwable $th) {
            return ['code' => $th->getCode(), 'msg' => $th->getMessage()];
        }
    }

    public function getLogin(Request $request)
    {
        // return response()
        //     ->json(['msg' => $request->header()], 200);
        $validateResult = $this->authValidate($request);
        if (!empty($validateResult)) return response()->json($validateResult, 401);

        if (!Auth::attempt($request->only('mem_id', 'password')))
        {
            return response()
                ->json(['code' => '2004', 'msg' => '회원 인증 실패'], 401);
        }

        $user = User::where('mem_id', $request['mem_id'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['code' => '1000', 'msg' => 'Hi '.$user->mem_id.', welcome', 'login_token' => $token ]);
    }

    public function setLogin(Request $request)
    {
        $validateResult = $this->authValidate($request);
        if (!empty($validateResult)) return response()->json($validateResult, 401);

        // login_token에 해당되는 인스턴스 찾기
        $instance = \Laravel\Sanctum\PersonalAccessToken::findToken($request->login_token);
        if (empty($instance)) {
            return response()
                ->json(['code' => '2004', 'msg' => '회원 인증 실패'], 401);
        }
        // 인스턴스로 해당되는 User 찾기
        $user = User::where('id', $instance->tokenable_id)->firstOrFail();
        // 로그인 처리
        Auth::login($user);
        $request->session()->regenerate();

        return response()
            ->json(['code' => '1000', 'msg' => 'Hi '.$user->mem_id.', welcome']);
    }
}
