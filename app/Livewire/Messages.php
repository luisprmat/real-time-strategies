<?php

namespace App\Livewire;

use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Messages extends Component
{
    public Collection $messages;

    public ?int $userId = null;

    public function mount()
    {
        if ($authUser = auth()->user()) {
            $this->userId = $authUser->id;
        }

        $this->messages = Message::where('private', false)
            ->orWhere(function (Builder $query) {
                $query->when($this->userId, function (Builder $q) {
                    $q->where('user_id', $this->userId);
                });
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.messages');
    }
}
