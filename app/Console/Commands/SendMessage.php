<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a message from server.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = select(
            __('Private or public?'),
            [__('Public'), __('Private')],
        );

        if ($type === __('Private')) {
            $userId = text(
                label: __('What is the user ID?'),
                default: '1',
            );
        }

        if (isset($userId)) {
            $message = text(
                label: __('What is your private message?'),
                default: __('User #:id, I have a secret to tell you!', ['id' => $userId]),
            );

            Message::create([
                'message' => $message,
                'user_id' => $userId,
                'private' => true,
            ]);

            $this->info(__('Message was sent!'));

            return;
        }

        $message = text(
            label: __('What is your public message?'),
            default: __('Hello everyone!'),
        );

        Message::create(['message' => $message]);

        $this->info(__('Message was sent!'));
    }
}
