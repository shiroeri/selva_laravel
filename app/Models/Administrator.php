<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrator extends Authenticatable
{
    use HasFactory, SoftDeletes;

    // 実際のテーブル名 'administers' を明示的に指定
    protected $table = 'administers';

    // 認証ガードの設定 (このプロパティは通常不要です)
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'login_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ★★★ 修正: 'password' => 'hashed' を削除します。★★★
        // パスワードのハッシュ化はコントローラー（Auth::attempt）またはDB挿入時に行います。
        'deleted_at' => 'datetime',
    ];
}
