<?php

declare(strict_types=1);

namespace Moco\Struct;

class Email
{
    public string $emails_to = '';
    public string $subject = '';
    public string $text = '';
    public ?string $emails_cc = null;
    public ?string $emails_bcc = null;
    public ?string $letter_paper_id = null;

    public static function fromArray(array $params): self
    {
        $email = new self();
        $email->emails_to = $params['emails_to'];
        ;
        $email->subject = $params['subject'];
        $email->text = $params['text'];
        $email->emails_cc = $params['emails_cc'] ?? null;
        $email->emails_bcc = $params['emails_bcc'] ?? null;
        $email->letter_paper_id = $params['letter_paper_id'] ?? null;
        return $email;
    }
}
