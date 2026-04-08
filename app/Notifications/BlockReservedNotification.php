<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class BlockReservedNotification extends Notification
{
    use Queueable;

    public $lot;
    public $supervisor;

    public function __construct($lot, $supervisor)
    {
        $this->lot = $lot;
        $this->supervisor = $supervisor;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('¡Lote Reservado!')
            ->icon('/icon-192.png')
            ->body("El supervisor {$this->supervisor} ha marcado como RESERVADO tu lote Mza {$this->lot->manzana}, Lote {$this->lot->nro_lote}.")
            ->action('Ver Inventario', '/inventory');
    }
}
