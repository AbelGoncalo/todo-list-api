<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\{Auth, DB,Hash, Log};
use Illuminate\Support\Str;

class AuthRepository
{
   protected $model;
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function createUser($data)
    {
        return $this->model->create($data);
    }

    public function updateUser($data, $id)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->update($data);
            return $user->fresh();

        } catch (ModelNotFoundException $e) {

            Log::error('User not found: ' . $e->getMessage());
            return $e->getMessage();

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
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
