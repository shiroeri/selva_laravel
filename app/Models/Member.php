<?php

namespace App\Models;

// 認証機能を持たせるために Authenticatable を use します
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Notifications\Notifiable; // 一般的に使用されるため追加

// ★Model ではなく Authenticatable を継承します★
class Member extends Authenticatable
{
    // 論理削除 (SoftDeletes) を維持します
    use SoftDeletes;
    use HasFactory;
    use Notifiable; // 通知機能を仮に追加

    /**
     * The attributes that are mass assignable.
     * マスアサインメントを許可する属性（カラム）
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_sei',
        'name_mei',
        'nickname',
        'gender',
        'password',
        'email',
        'auth_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * 認証情報として外部に出力される際に隠蔽する属性（パスワードなど）
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // 認証のために必要な場合があるため追加
    ];
    
    // --- 【追加】レビューモデルとのリレーション ---
    /**
     * このメンバーが投稿したレビューとのリレーションを定義します。
     */
    public function reviews()
    {
        // 外部キー名はデフォルトの member_id が使用されていると仮定します
        return $this->hasMany(Review::class, 'member_id');
    }
    
    // --- 【追加】フルネームを取得するためのアクセサ ---
    /**
     * ビュー側で $review->member->name とアクセスされたときに、
     * 姓（name_sei）と名（name_mei）を結合したフルネームを返します。
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        // 姓と名を結合してフルネームを返します。
        // スペース区切りが必要な場合は return $this->name_sei . ' ' . $this->name_mei; に変更してください。
        return $this->name_sei . $this->name_mei; 
    }
}
