<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Repositories\NoteRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    protected $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function index()
    {
        $user = auth()->user();
        // Проверяем, есть ли кэшированные данные для этого запроса
        $cacheKey = 'user_' . $user->id . '_notes';
        $notes = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $this->noteRepository->allByUserId($user->id);
        });

        // Логирования события
        Log::info('User ' . $user->id . ' accessed notes.');

        return response()->json($notes);
    }

    public function store(StoreNoteRequest $request)
    {
        $user = auth()->user();
        $noteData = $request->validated();
        $noteData['user_id'] = $user->id;
        $note = $this->noteRepository->create($noteData);

        // Логирования события
        Log::info('User ' . $user->id . ' created a new note with ID: ' . $note->id);

        return response()->json($note, 201);
    }

    public function show($id)
    {
        $note = $this->noteRepository->find($id);
        if (!$note) {
            Log::error('Note with ID ' . $id . ' not found.');

            return response()->json(['error' => 'Note not found'], 404);
        }
        if ($note->user_id !== auth()->id()) {
            // Пример логирования ошибки доступа
            Log::warning('Unauthorized attempt to access note with ID ' . $id . ' by user ' . auth()->id());

            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Логирования успешного доступа к заметке
        Log::info('User ' . auth()->id() . ' accessed note with ID: ' . $note->id);

        return response()->json($note);
    }

    public function update($id, UpdateNoteRequest $request)
    {
        $note = $this->noteRepository->find($id);
        if (!$note) {
            // Логирования ошибки
            Log::error('Note with ID ' . $id . ' not found.');

            return response()->json(['error' => 'Note not found'], 404);
        }
        if ($note->user_id !== auth()->id()) {
            // Логирования ошибки доступа
            Log::warning('Unauthorized attempt to update note with ID ' . $id . ' by user ' . auth()->id());

            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $note = $this->noteRepository->update($id, $request->validated());

        // Логирования успешного обновления заметки
        Log::info('User ' . auth()->id() . ' updated note with ID: ' . $note->id);

        return response()->json($note);
    }

    public function delete($id)
    {
        $note = $this->noteRepository->find($id);
        if (!$note) {
            // Логирования ошибки
            Log::error('Note with ID ' . $id . ' not found.');

            return response()->json(['error' => 'Note not found'], 404);
        }
        if ($note->user_id !== auth()->id()) {
            // Логирования ошибки доступа
            Log::warning('Unauthorized attempt to delete note with ID ' . $id . ' by user ' . auth()->id());

            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $this->noteRepository->delete($id);

        // Логирования успешного удаления заметки
        Log::info('User ' . auth()->id() . ' deleted note with ID: ' . $id);

        return response()->json(['message' => 'Note deleted']);
    }
}
