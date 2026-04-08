<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class BlocksExpiredNotification extends Notification
{
    use Queueable;

    public $lotsInfo;

    public function __construct(array $lotsInfo)
    {
        // Array of string like "Mza 1 Lote 2"
        $this->lotsInfo = $lotsInfo;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $lotsStr = implode(', ', $this->lotsInfo);
        
        return (new WebPushMessage)
            ->title('Bloqueos Expirados')
            ->icon('/icon-192.png')
            ->body("Los siguientes bloqueos han expirado y los lotes están disponibles: $lotsStr.")
            ->action('Ver Inventario', '/inventory');
    }
}
