<?php

namespace App\Notifications;

use App\Models\History;
use App\Models\Server;
use App\Notifications\Traits\NotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DeploymentComplete extends Notification
{
    use Queueable;
    use NotificationChannels;

    /**
     * History
     * @var \App\Models\Server
     */
    protected $server;

    /**
     * History
     * @var \App\Models\History
     */
    protected $history;

    /**
     * Create the deployment complete notification instance.
     *
     * @param  \App\Models\Server  $server  Server object
     * @param  \App\Models\History $history History object
     *
     */
    public function __construct(Server $server, History $history)
    {
        $this->server = $server;
        $this->history = $history;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->enabledChannels($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $from = $this->history->from_commit;
        $to = $this->history->to_commit;
        $successfully = $this->history->success ? "successfully" : "unsuccessfully";

        return (new MailMessage)
                    ->line($this->primaryMessage())
                    ->line("The server was {$successfully} deployed from {$from} to {$to}");
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = (new SlackMessage)
            ->content($this->primaryMessage())
            ->attachment(function ($attachment) {
                 $attachment->title('Details')
                            ->fields([
                                    'From' => $this->history->from_commit,
                                    'To' => $this->history->to_commit,
                                    'Branch' => $this->server->branch,
                                    'By' => $this->history->user_name
                                ]);
            });

        if ($this->history->success === false) {
            $message->error();
        }
        return $message;
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'project' => $this->server->project->name,
            'server' => $this->server->name,
            'host' => $this->server->hostname,
            'branch' => $this->server->branch,
            'from' => $this->history->from_commit,
            'to' => $this->history->to_commit,
            'by' => $this->history->user_name,
        ];
    }

    private function primaryMessage()
    {
        return "{$this->server->project->name} deployed to {$this->server->name} ({$this->server->hostname})";
    }
}
