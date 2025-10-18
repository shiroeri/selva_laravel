<?php

// app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 論理削除を使う場合

class Member extends Model
{
    use SoftDeletes; // deleted_at を使うため追加

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
        // 'created_at', 'updated_at' は timestamps() で自動処理されるため不要
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