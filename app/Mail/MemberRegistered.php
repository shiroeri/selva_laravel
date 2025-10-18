<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberRegistered extends Mailable
{
    use Queueable, SerializesModels;

    // ★Public プロパティとして Member モデルを定義★
    public $member;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member)
    {
        // ★コンストラクタで受け取った Member をプロパティにセット★
        $this->member = $member;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【Laravel課題】会員登録が完了しました', // メール件名を設定
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member_registered',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
