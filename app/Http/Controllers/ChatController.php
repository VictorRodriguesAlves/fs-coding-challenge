<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\User;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $chatService
    ) {}

    public function index(Request $request): Response
    {
        $user = User::find(1);
        $selectedContactId = (int) $request->input('contact_id');
        $pageData = $this->chatService->getPageData($user, $selectedContactId);

        return Inertia::render('Chat/Index', $pageData);
    }
}