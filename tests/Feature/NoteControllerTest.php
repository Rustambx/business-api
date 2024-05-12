<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_all_notes()
    {
        // Создаем пользователя
        $user = \App\Models\User::factory()->create();

        // Аутентифицируем пользователя
        $this->actingAs($user);

        // Создаем несколько заметок для этого пользователя
        $notes = \App\Models\Note::factory(3)->create(['user_id' => $user->id]);

        // Делаем запрос к методу index
        $response = $this->get('/api/notes');

        // Проверяем, что запрос вернул успешный ответ и данные о заметках
        $response->assertStatus(200)->assertJson($notes->toArray());
    }

    public function test_user_can_create_note()
    {
        // Создаем пользователя
        $user = \App\Models\User::factory()->create();

        // Аутентифицируем пользователя
        $this->actingAs($user);

        // Подготавливаем данные для создания заметки
        $noteData = \App\Models\Note::factory()->raw(['user_id' => $user->id]);

        // Делаем запрос к методу store
        $response = $this->post('/api/notes', $noteData);

        // Проверяем, что запрос вернул успешный ответ и создана новая заметка
        $response->assertStatus(201)->assertJson($noteData);
    }

    public function test_user_can_view_own_note()
    {
        // Создаем пользователя
        $user = \App\Models\User::factory()->create();

        // Аутентифицируем пользователя
        $this->actingAs($user);

        // Создаем заметку для этого пользователя
        $note = \App\Models\Note::factory()->create(['user_id' => $user->id]);

        // Делаем запрос к методу show
        $response = $this->get('/api/notes/' . $note->id);

        // Проверяем, что запрос вернул успешный ответ и данные о заметке
        $response->assertStatus(200)->assertJson($note->toArray());
    }

    public function test_user_cannot_view_other_user_note()
    {
        // Создаем двух пользователей
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        // Аутентифицируем первого пользователя
        $this->actingAs($user1);

        // Создаем заметку для второго пользователя
        $note = \App\Models\Note::factory()->create(['user_id' => $user2->id]);

        // Делаем запрос к методу show с ID заметки пользователя 2
        $response = $this->get('/api/notes/' . $note->id);

        // Проверяем, что запрос вернул ошибку 403 (Forbidden)
        $response->assertStatus(403);
    }

    public function test_user_can_update_own_note()
    {
        // Создаем пользователя
        $user = \App\Models\User::factory()->create();

        // Аутентифицируем пользователя
        $this->actingAs($user);

        // Создаем заметку для этого пользователя
        $note = \App\Models\Note::factory()->create(['user_id' => $user->id]);

        // Подготавливаем данные для обновления заметки
        $updatedNoteData = ['title' => 'Updated Title', 'content' => 'Updated Content'];

        // Делаем запрос к методу update
        $response = $this->post('/api/notes/update/' . $note->id, $updatedNoteData);

        // Проверяем, что запрос вернул успешный ответ и данные о заметке обновлены
        $response->assertStatus(200)->assertJson($updatedNoteData);
    }

    public function test_user_can_delete_own_note()
    {
        // Создаем пользователя
        $user = \App\Models\User::factory()->create();

        // Аутентифицируем пользователя
        $this->actingAs($user);

        // Создаем заметку для этого пользователя
        $note = \App\Models\Note::factory()->create(['user_id' => $user->id]);

        // Делаем запрос к методу delete
        $response = $this->delete('/api/notes/delete/' . $note->id);

        // Проверяем, что запрос вернул успешный ответ и заметка удалена
        $response->assertStatus(200)->assertJson(['message' => 'Note deleted']);
    }
}
