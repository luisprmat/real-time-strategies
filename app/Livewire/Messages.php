<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

class Messages extends Component
{
    public array $messages = [];

    public function mount()
    {
        // Hard-coded messages
        $this->messages = [
            [
                'time' => Carbon::now()->format('g:i:sa'),
                'message' => 'Hola a todos',
                'private' => false,
            ],
            [
                'time' => Carbon::now()->format('g:i:sa'),
                'message' => 'Hola usuario privado',
                'private' => true,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.messages');
    }
}
