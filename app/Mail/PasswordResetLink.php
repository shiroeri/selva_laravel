<?php

namespace App\Mail;

use App\Models\Member; 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $token;
    public $resetUrl;

    public function __construct(Member $member, $token)
    {
        $this->member = $member;
        $this->token = $token;

        // URLをここで生成しプロパティに格納★
        $this->resetUrl = route('password.reset', [
            'token' => $this->token, 
            'email' => $this->member->email
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'パスワード再設定',
        );
    }

    public function content(): Content
    {
        // 実際の再設定URLを生成
        // route() ヘルパーを使用して、トークンとメールアドレスを含むリンクを生成します
        $resetUrl = route('password.reset', [
            'token' => $this->token, 
            'email' => $this->member->email
        ]);
        
        // ★★★ 修正箇所: HTML文字列を Content クラスの html: キーに渡す ★★★
        $htmlContent = '
            <h1>パスワード再設定</h1>
            <p>このメールはパスワード再設定のために自動送信されています。</p>
            <p>会員名: ' . $this->member->name_sei . ' ' . $this->member->name_mei . '様</p>
            <p>以下のリンクをクリックして、パスワードの再設定を完了してください。</p>
            <p>リンク: <a href="' . $resetUrl . '">' . $resetUrl . '</a></p>
            <p>※このメールに心当たりのない場合は、破棄してください。</p>
        ';
        
        return new Content(
            // html: キーを使用し、生のHTML文字列をメール本文として指定します。
            view: 'emails.password-reset-link',
        );
    }
}