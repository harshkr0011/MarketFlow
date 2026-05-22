<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class CollaborationChat extends Component
{
    public $workspaceId = 0;
    public $messages = [];
    public $newMessage = '';
    public $campaignTitle = '';
    public $campaignStatus = '';
    public $annotations = [];

    public function mount($workspaceId = null)
    {
        $this->workspaceId = $workspaceId ?? 0;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if (empty($this->workspaceId)) {
            $this->messages = [];
            $this->annotations = [];
            return;
        }

        $this->messages = \App\Models\Message::where('workspace_id', $this->workspaceId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        // Parse annotations from messages
        $this->annotations = [];
        foreach ($this->messages as $msg) {
            if (str_starts_with($msg['content'], '📍 [ANNOTATION]')) {
                if (preg_match('/\(x:([\d\.]+)%,\s*y:([\d\.]+)%\):\s*(.*)/i', $msg['content'], $matches)) {
                    $this->annotations[] = [
                        'id' => $msg['id'],
                        'x' => $matches[1],
                        'y' => $matches[2],
                        'text' => $matches[3],
                        'user' => $msg['user']['name'] ?? 'Client',
                    ];
                }
            }
        }

        $campaign = \App\Models\Campaign::where('workspace_id', $this->workspaceId)->first();
        if ($campaign) {
            $this->campaignTitle = $campaign->title;
            $this->campaignStatus = $campaign->status;
        } else {
            // Seed a default campaign if none exists
            $campaign = \App\Models\Campaign::create([
                'user_id' => auth()->id(),
                'workspace_id' => $this->workspaceId,
                'title' => 'Summer Launch Campaign',
                'description' => 'Global marketing push for summer products',
                'status' => 'Idea',
            ]);
            $this->campaignTitle = $campaign->title;
            $this->campaignStatus = $campaign->status;
        }
    }

    public function addAnnotation($x, $y, $comment)
    {
        if (empty($this->workspaceId)) {
            return;
        }

        $this->validate([
            'workspaceId' => 'required'
        ]);

        $content = "📍 [ANNOTATION] (x:{$x}%, y:{$y}%): {$comment}";

        $message = \App\Models\Message::create([
            'workspace_id' => $this->workspaceId,
            'user_id' => auth()->id(),
            'content' => $content,
        ]);

        try {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Ignore broadcasting errors
        }

        $this->loadMessages();
        $this->dispatch('message-sent');
        $this->dispatch('show-toast', message: 'Visual feedback added to the creative review!', type: 'success');
    }

    public function updateCampaignStatus($status)
    {
        if (empty($this->workspaceId)) {
            return;
        }

        $campaign = \App\Models\Campaign::where('workspace_id', $this->workspaceId)->first();
        if ($campaign) {
            $oldStatus = $campaign->status;
            $campaign->update(['status' => $status]);

            // Add a system message in the chat
            $message = \App\Models\Message::create([
                'workspace_id' => $this->workspaceId,
                'user_id' => auth()->id(),
                'content' => "📢 [SYSTEM]: Campaign status updated from '{$oldStatus}' to '{$status}' by client " . auth()->user()->name,
            ]);

            try {
                broadcast(new \App\Events\MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                // Ignore broadcasting errors if Reverb is not configured/running
            }

            $this->loadMessages();
            $this->dispatch('message-sent');
        }
    }

    public function sendMessage()
    {
        if (empty($this->workspaceId)) {
            return;
        }

        $this->validate([
            'newMessage' => 'required|string|max:1000'
        ]);

        $message = \App\Models\Message::create([
            'workspace_id' => $this->workspaceId,
            'user_id' => auth()->id(),
            'content' => $this->newMessage,
        ]);

        try {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Ignore broadcasting errors if Reverb is not configured/running
        }

        $this->newMessage = '';
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    #[On('echo-private:workspaces.{workspaceId},MessageSent')]
    public function onMessageReceived($event)
    {
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.collaboration-chat');
    }
}
