<?php

namespace DodoPayments\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookReceived
{
    use Dispatchable, SerializesModels;

    public string $type;
    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
