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


// Atualizar status da tarefa
it('should update task status', function () {
    $user = authUser();

    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $payload = ['status' => 'completed'];

    putJson("/api/tasks/{$task->id}", $payload)
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed']);
});

// Deletar tarefa
test('should delete a task', function () {
    $user = authUser();

    $task = Task::factory()->create(['user_id' => $user->id]);

    deleteJson("/api/tasks/{$task->id}")
        ->assertOk()
        ->assertJson(['message' => 'Task deleted successfully!']);

    expect(Task::find($task->id))->toBeNull();
});

// Filtrar tarefas por status
test('should filter tasks by status', function () {
    $user = authUser();

    Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
    Task::factory()->create(['user_id' => $user->id, 'status' => 'completed']);

    postJson('/api/tasks/status', ['status' => 'completed'])
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed'])
        ->assertJsonMissing(['status' => 'pending']);
});
