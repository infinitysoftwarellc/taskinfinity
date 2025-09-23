<?php

namespace Tests\Feature\Folder;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderControllerTest extends TestCase
{
    use RefreshDatabase; // Essencial: Reseta o banco de dados para cada teste

    /** @test */
    public function an_authenticated_user_can_create_a_folder()
    {
        // 1. Prepara o ambiente
        $user = User::factory()->create();

        // 2. Simula a ação
        $response = $this->actingAs($user)->post('/folders', [
            'name' => 'Meu Primeiro Projeto',
            'description' => 'Descrição do projeto.',
        ]);

        // 3. Verifica o resultado
        $this->assertDatabaseHas('folders', [
            'user_id' => $user->id,
            'name' => 'Meu Primeiro Projeto',
        ]);

        $response->assertRedirect('/folders'); // Assumindo que a rota de redirect é /folders/index
    }

    /** @test */
    public function an_authenticated_user_can_update_their_own_folder()
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/folders/' . $folder->id, [
            'name' => 'Nome Atualizado',
            'description' => 'Descrição atualizada.',
        ]);

        $this->assertDatabaseHas('folders', [
            'id' => $folder->id,
            'name' => 'Nome Atualizado',
        ]);

        $response->assertRedirect('/folders');
    }

    /** @test */
    public function an_authenticated_user_can_delete_their_own_folder()
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/folders/' . $folder->id);

        $this->assertDatabaseMissing('folders', ['id' => $folder->id]);

        $response->assertRedirect('/folders');
    }

    /** @test */
    public function a_user_cannot_update_another_users_folder()
    {
        // Usuário A (dono da pasta)
        $owner = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);

        // Usuário B (o invasor)
        $intruder = User::factory()->create();

        // Tentativa de atualização pelo invasor
        $response = $this->actingAs($intruder)->put('/folders/' . $folder->id, [
            'name' => 'Nome Malicioso',
        ]);

        // Verifica se a atualização foi bloqueada (erro 403 Forbidden)
        $response->assertStatus(403);
    }
}