<?php


use App\Models\{User, Task};
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\{postJson, getJson, putJson, deleteJson};

uses(RefreshDatabase::class);

// Função auxiliar para autenticar usuário
function authUser(): User
{
    $user = User::factory()->create([
        'password' => bcrypt('password'), // garante que a senha seja válida
    ]);

    $response = postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'pest_test',
    ]);

    $token = $response->json('access_token'); // ajuste aqui conforme o seu retorno real

    test()->withToken($token);

    return $user;
}

// Criar tarefa
test('should create a task', function () {
    $user = authUser();


    $data = [
        'title' => 'Nova Tarefa',
        'description' => 'Descrição da tarefa',
        'user_id' => $user->id,
    ];

    postJson('/api/tasks', $data)
        ->assertCreated()
        ->assertJsonFragment(['title' => 'Nova Tarefa']);
});

// Listar tarefas do usuário
test('should list tasks of authenticated user', function () {

    $user = authUser();

    Task::factory()->count(3)->create(['user_id' => $user->id]);

    getJson('/api/tasks')
        ->assertOk()
        ->assertJsonCount(3);
});
