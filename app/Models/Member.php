<?php

namespace App\Models;

// 認証機能を持たせるために Authenticatable を use します
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Eloquentモデルに通常必要なトレイト

// ★Model ではなく Authenticatable を継承します★
class Member extends Authenticatable
{
    // 論理削除 (SoftDeletes) を維持します
    use SoftDeletes;
    use HasFactory; // 必要に応じて HasFactory を追加

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

    /**
     * パスワードをハッシュ化するため、ハッシュ化済みの値がセットされるように上書き（オプション）
     *
     * @param string $value
     * @return void
     */
    // protected function setPasswordAttribute(string $value): void
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }
}