<?php

namespace App\Jobs;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\EmailService;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailLog;

    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }

    public function handle(EmailService $emailService)
    {
        try {

            $emailService->send($this->emailLog);

            $this->emailLog->update([
                'status' => 'sent'
            ]);
        } catch (\Exception $e) {

            $this->emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
