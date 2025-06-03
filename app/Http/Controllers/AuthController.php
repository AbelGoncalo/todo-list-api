<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function createUser(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:5|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6|max:6',
                'confirm_password' => 'required|min:6|max:6|same:password',
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.string' => 'O nome deve ser um texto.',
                'name.min' => 'O nome deve ter no mínimo :min caracteres.',
                'name.max' => 'O nome deve ter no máximo :max caracteres.',

                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
                'email.max' => 'O e-mail deve ter no máximo :max caracteres.',
                'email.unique' => 'Este e-mail já está cadastrado.',

                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter exatamente :min caracteres.',
                'password.max' => 'A senha deve ter exatamente :max caracteres.',

                'confirm_password.required' => 'A confirmação de senha é obrigatória.',
                'confirm_password.min' => 'A confirmação de senha deve ter exatamente :min caracteres.',
                'confirm_password.max' => 'A confirmação de senha deve ter exatamente :max caracteres.',
                'confirm_password.same' => 'A confirmação de senha deve ser igual à senha.',
            ]);


            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors()], 422);
            }

            $user = $this->authService->createUser($request->except(["confirm_password"]));

            if (!$user) {
                return response()->json(["success" => false, "message" => $user, "data" => null], 422);
            }

            return response()->json(["success" => true, "data" => $user, "message" => "User created!"], 200);
        } catch (\Throwable $th) {

            return response()->json(["success" => false, "message" => $th, "data" => null], 500);
        }
    }


    public function login(Request $request)
    {
        // Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tenta autenticar usando os dados fornecidos
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: invalid credentials.'
            ], 401);
        }


        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 // tempo em segundos
        ]);
    }

    public function logout()
    {

        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
            'data' =>  []
        ], 200);
    }
}
