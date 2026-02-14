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

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailLog;

    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }

    public function handle()
    {
        try {

            $to = explode(';', $this->emailLog->to);
            $cc = $this->emailLog->cc ? explode(';', $this->emailLog->cc) : [];
            $bcc = $this->emailLog->bcc ? explode(';', $this->emailLog->bcc) : [];
            $reply = $this->emailLog->reply_to ? explode(';', $this->emailLog->reply_to) : [];

            Mail::send([], [], function ($message) use ($to, $cc, $bcc, $reply) {

                $message->to($to)
                    ->subject($this->emailLog->subject)
                    ->html($this->emailLog->body);
                // ->setBody($this->emailLog->body, 'text/html');

                if (!empty($cc)) $message->cc($cc);
                if (!empty($bcc)) $message->bcc($bcc);
                if (!empty($reply)) $message->replyTo($reply);

                // Attachments
                if ($this->emailLog->attachments) {
                    foreach ($this->emailLog->attachments as $file) {

                        $fileContent = Http::get($file['url'])->body();

                        $message->attachData(
                            $fileContent,
                            $file['filename'],
                            ['mime' => $file['mime']]
                        );
                    }
                }
            });

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
