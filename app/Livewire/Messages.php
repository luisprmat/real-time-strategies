<?php

namespace App\Livewire;

use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Messages extends Component
{
    public ?int $userId = null;

    public Collection $messages;

    public function getListeners()
    {
        $userId = auth()->user()->id;

        return [
            'echo:public.newMessage,.MessageCreated' => 'addMessage',
            "echo-private:private.{$userId}.newMessage,.MessageCreated" => 'addPrivateMessage',
        ];
    }

    public function addMessage(array $event)
    {
        $this->messages[] = [
            'time' => $event['model']['created_at'],
            'message' => $event['model']['message'],
            'private' => $event['model']['private'],
        ];
    }

    public function addPrivateMessage(array $event)
    {
        $this->addMessage($event, true);
    }

    public function render()
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

        return view('livewire.messages');
    }
}
