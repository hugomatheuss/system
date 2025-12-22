<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMessage;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MessageController
{
    public function index(): JsonResponse
    {
        $messages = Message::latest()->paginate(15);

        return response()->json($messages);
    }

    public function show(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        return response()->json($message);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        $message = Message::create($data);

        ProcessMessage::dispatch($message->id);

        return response()->json($message, 201);
    }
}
