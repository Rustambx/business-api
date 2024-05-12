<?php

namespace App\Repositories;

use App\Models\Note;

class NoteRepository
{
    protected $model;

    public function __construct(Note $note)
    {
        $this->model = $note;
    }

    public function allByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $note = $this->find($id);
        $note->update($data);
        return $note;
    }

    public function delete($id)
    {
        $note = $this->find($id);
        $note->delete();
        return true;
    }
}
