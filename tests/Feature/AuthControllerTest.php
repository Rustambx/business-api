<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест на успешную регистрацию пользователя.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    /**
     * Тест на успешный вход пользователя.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    /**
     * Тест на успешный выход пользователя.
     *
     * @return void
     */
    public function test_user_can_logout()
    {
        // Создаем пользователя
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Авторизуем пользователя и получаем токен
        $token = $this->getTokenForUser($user);

        // Делаем запрос на logout с использованием Bearer токена
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/logout');

        // Проверяем успешность запроса и ожидаемый ответ
        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    /**
     * Тест на получение информации о текущем пользователе.
     *
     * @return void
     */
    public function test_user_can_get_current_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Авторизуем пользователя и получаем токен
        $token = $this->getTokenForUser($user);

        // Ключ кэша для данных текущего пользователя
        $cacheKey = 'user_' . $user->id . '_profile';

        // Получаем данные о пользователе из кэша, если они там есть
        $cachedUserData = Cache::get($cacheKey);

        // Если данные не найдены в кэше, делаем запрос к API
        if (!$cachedUserData) {
            // Делаем запрос к /api/me для получения информации о текущем пользователе
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->postJson('/api/me');

            // Получаем данные ответа и кэшируем их на 30 минут
            $responseData = $response->decodeResponseJson();
            Cache::put($cacheKey, $responseData, now()->addMinutes(30));
        }

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
    }

    protected function getTokenForUser($user)
    {
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $responseData = $response->decodeResponseJson();
        return $responseData['access_token'];
    }
}
