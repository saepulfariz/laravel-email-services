<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\EmailLog;

class EmailService
{
    public function send(EmailLog $emailLog)
    {
        $to = explode(';', $emailLog->to);
        $cc = $emailLog->cc ? explode(';', $emailLog->cc) : [];
        $bcc = $emailLog->bcc ? explode(';', $emailLog->bcc) : [];
        $reply = $emailLog->reply_to ? explode(';', $emailLog->reply_to) : [];

        $type = $emailLog->type === 'public' ? 'public' : 'smtp';

        Mail::mailer($type)->send([], [], function ($message) use ($emailLog, $to, $cc, $bcc, $reply, $type) {

            if ($type === 'public') {
                $message->from(
                    config('mail.mailers.public.username'),
                    env('MAIL_PUBLIC_FROM_NAME')
                );
            }

            $message->to($to)
                ->subject($emailLog->subject)
                ->html($emailLog->body);

            if (!empty($cc)) $message->cc($cc);
            if (!empty($bcc)) $message->bcc($bcc);
            if (!empty($reply)) $message->replyTo($reply);

            if ($emailLog->attachments) {
                foreach ($emailLog->attachments as $file) {
                    $fileContent = Http::get($file['url'])->body();

                    $message->attachData(
                        $fileContent,
                        $file['filename'],
                        ['mime' => $file['mime']]
                    );
                }
            }
        });

        $emailLog->update(['status' => 'sent']);
    }
}
