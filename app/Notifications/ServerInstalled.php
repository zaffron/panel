<?php

namespace App\Notifications;

use App\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use App\Contracts\Core\ReceivesEvents;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\MailMessage;

class ServerInstalled extends Notification implements ShouldQueue, ReceivesEvents
{
    use Queueable;

    /**
     * @var \App\Models\Server
     */
    public $server;

    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * Handle a direct call to this notification from the server installed event. This is configured
     * in the event service provider.
     *
     * @param \App\Events\Event|\App\Events\Server\Installed $event
     */
    public function handle(Event $event): void
    {
        $event->server->loadMissing('user');

        $this->server = $event->server;
        $this->user = $event->server->user;

        // Since we are calling this notification directly from an event listener we need to fire off the dispatcher
        // to send the email now. Don't use send() or you'll end up firing off two different events.
        Container::getInstance()->make(Dispatcher::class)->sendNow($this->user, $this);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->user->username . '.')
            ->line('Your server has finished installing and is now ready for you to use.')
            ->line('Server Name: ' . $this->server->name)
            ->action('Login and Begin Using', route('index'));
    }
}
