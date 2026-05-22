<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiService;

class SupportCenter extends Component
{
    // Ticket properties
    public $subject = '';
    public $description = '';
    public $priority = 'Medium';
    public $isRaisingTicket = false;

    // Chatbot properties
    public $chatInput = '';
    public $chatHistory = [];
    public $isChatLoading = false;

    public function mount()
    {
        // Add welcome message from AI
        $this->chatHistory[] = [
            'sender' => 'ai',
            'text' => 'Hello! I am your MarketFlow AI Support Assistant. How can I help you optimize your marketing agency workflow today?'
        ];
    }

    public function sendChatMessage()
    {
        $this->validate([
            'chatInput' => 'required|string|min:2|max:1000'
        ]);

        $userMessage = $this->chatInput;
        $this->chatHistory[] = [
            'sender' => 'user',
            'text' => $userMessage
        ];

        $this->chatInput = '';
        $this->isChatLoading = true;

        // Perform async generation (or synchronous for immediate response)
        $gemini = new GeminiService();
        
        $systemPrompt = "You are MarketFlow's AI support chatbot. You help agency marketers and staff with campaigns, CRM leads, assets vault, billing, and support. Be concise, extremely professional, and friendly. If a user has a complex bug or billing issue, recommend that they raise a formal support ticket using the Support Tickets form on this page.";
        
        // Build recent context from history to give Gemini some memory
        $context = "";
        foreach (array_slice($this->chatHistory, -6) as $msg) {
            $context .= ($msg['sender'] === 'user' ? "User: " : "AI: ") . $msg['text'] . "\n";
        }

        $reply = $gemini->generateContent($systemPrompt, $context . "\nUser: " . $userMessage);

        if (str_starts_with($reply, 'Error:') || str_starts_with($reply, 'An exception')) {
            $reply = "I apologize, but I am having trouble connecting to my central engine. You can raise a support ticket on the left pane and one of our agency managers will reach out to you shortly!";
        }

        $this->chatHistory[] = [
            'sender' => 'ai',
            'text' => $reply
        ];

        $this->isChatLoading = false;
    }

    public function raiseTicket()
    {
        $this->validate([
            'subject' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:1000',
            'priority' => 'required|in:Low,Medium,High',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'Open',
        ]);

        $this->subject = '';
        $this->description = '';
        $this->priority = 'Medium';
        $this->isRaisingTicket = false;

        $this->dispatch('show-toast', message: 'Support ticket raised successfully!', type: 'success');
    }

    public function render()
    {
        $tickets = Ticket::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();

        return view('livewire.support-center', compact('tickets'));
    }
}
