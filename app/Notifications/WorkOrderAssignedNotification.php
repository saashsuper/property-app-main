<?php

namespace App\Notifications;

use App\Models\BlockWorkOrder;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WorkOrderAssignedNotification extends Notification
{
    public function __construct(
        public BlockWorkOrder $workOrder,
        public string $action = 'assigned'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, \Illuminate\Notifications\NotificationChannel>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $titles = [
            'assigned' => 'New Work Order Assigned',
            'updated' => 'Work Order Assignment Updated',
            'reassigned' => 'Work Order Reassigned to You',
        ];
        $title = $titles[$this->action] ?? 'Work Order Assigned';
        $body = "Work order {$this->workOrder->ref_no} has been {$this->action} to your company.";

        // PWA deep link: /work-order/{id} (relative - SW uses same origin)
        $workOrderPath = '/work-order/' . $this->workOrder->id;

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon('/favicon.ico')
            ->action('View Work Order', 'view_work_order')
            ->data([
                'url' => $workOrderPath,
                'work_order_id' => (string) $this->workOrder->id,
                'ref_no' => $this->workOrder->ref_no ?? '',
                'action' => $this->action,
            ]);
    }
}
