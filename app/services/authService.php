<?php
namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;

class AuthService {

    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function createUser($data)
    {

        try {
            $values = [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password'],)
            ];

            return $this->authRepository->createUser($values);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function updateUser($data, $id)
    {

        try {

            $values = [
                'name' => $data['name'],
                'email' => $data['email'] ?? null
            ];

            return $this->authRepository->updateUser($values, $id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}


