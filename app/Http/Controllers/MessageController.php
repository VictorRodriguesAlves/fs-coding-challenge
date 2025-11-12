<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Jobs\SendMessageJob;
use App\Models\Message;
use App\Services\Chat\MessageService;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) {}

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();
        $this->messageService->sendMessage($validatedData, $user);
        return back();
    }
}
