<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class BlogCatalog extends Component
{
    public $search = '';
    public $selectedPost = null;

    public function selectPost($postId)
    {
        $this->selectedPost = Post::find($postId);
    }

    public function closePost()
    {
        $this->selectedPost = null;
    }

    public function render()
    {
        $query = Post::where('is_published', true);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        $posts = $query->orderBy('published_at', 'desc')->get();

        return view('livewire.blog-catalog', compact('posts'));
    }
}
