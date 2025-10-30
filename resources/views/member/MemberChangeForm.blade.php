<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報変更フォーム</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #FEF2CB; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); width: 100%; max-width: 500px; }
        h1 { text-align: center; color: #333; margin-bottom: 30px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        
        /* 氏名グループ全体を横並びにするためのスタイル */
        .name-form-content {
            display: flex;
            align-items: center; /* 垂直方向の中央揃え */
            gap: 15px;
        }

        /* フォームラベルのスタイル調整 */
        .form-group > label { 
            display: inline-block; /* 横並びにするためにインラインブロックに変更 */
            margin-bottom: 0; 
            margin-right: 15px; /* 右側に余白 */
            min-width: 60px; /* 氏名ラベルの幅を確保 */
        }
        
        input[type="text"] { padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        
        /* 姓と名のフィールドコンテナ */
        .name-fields-container {
            display: flex;
            gap: 15px; /* 姓と名のグループ間の間隔 */
            flex-grow: 1; /* フォームの残りのスペースを占める */
        }

        /* 姓・名それぞれのグループ (ラベルと入力欄) */
        .name-part {
            display: flex;
            align-items: center;
            gap: 5px; /* 「姓」「名」のテキストと入力欄の間隔 */
        }

        /* 「姓」「名」のテキスト部分のスタイル調整 */
        .name-part span {
            font-weight: bold;
            color: #555;
            white-space: nowrap;
        }

        /* 入力欄の幅を調整 */
        .name-part input[type="text"] {
            width: 120px; /* 入力欄の固定幅 */
        }

        /* エラーメッセージは個別に配置し、幅いっぱいに広げます */
        .error-message-group { margin-top: 5px; }

        .radio-group { display: flex; gap: 20px; align-items: center; }
        .radio-group label { font-weight: normal; margin-bottom: 0; }
        .radio-group input[type="radio"] { margin-right: 5px; }
        .error-message { color: #e3342f; font-size: 0.9em; margin-top: 5px; }
        .submit-button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-size: 18px; cursor: pointer; transition: background-color 0.3s; margin-top: 20px; }
        .submit-button:hover { background-color: #0056b3; }
        
        /* マイページに戻るボタンのコンテナ */
        .back-to-mypage-container {
            text-align: center; 
            margin-top: 15px;
        }

        /* マイページに戻るボタン */
        .back-to-mypage-button {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600; 
            color: #1d4ed8; 
            background-color: #dbeafe; 
            border-radius: 8px; 
            text-decoration: none;
            transition: background-color 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); 
            
        }

        .back-to-mypage-button:hover {
            background-color: #bfdbfe; 
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>会員情報登録</h1>

        @if (session('error'))
            <div class="error-message" style="text-align: center; margin-bottom: 15px; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.edit.confirm') }}">
            @csrf

            <!-- 1. 氏名 (ラベル + 姓グループ + 名グループを横並び) -->
            <div class="form-group">
                <div class="name-form-content">
                    <!-- 氏名ラベル -->
                    <label>氏名</label> 

                    <!-- 姓と名のフィールドを横並びにするコンテナ -->
                    <div class="name-fields-container">
                        
                        <!-- 姓のグループ (姓 + 入力欄) -->
                        <div class="name-part">
                            <span>姓</span>
                            <input type="text" name="name_sei" 
                                   value="{{ old('name_sei', $member->name_sei ?? '') }}">
                        </div>
                        
                        <!-- 名のグループ (名 + 入力欄) -->
                        <div class="name-part">
                            <span>名</span>
                            <input type="text" name="name_mei" 
                                   value="{{ old('name_mei', $member->name_mei ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- エラーメッセージは横並びの下に表示 -->
                <div class="error-message-group">
                    @error('name_sei')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    @error('name_mei')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 2. ニックネーム -->
            <div class="form-group">
                <label for="nickname">ニックネーム</label>
                <input type="text" id="nickname" name="nickname" placeholder="ニックネーム" 
                       value="{{ old('nickname', $member->nickname ?? '') }}" style="width: 100%;">
                @error('nickname')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. 性別 -->
            <div class="form-group">
                <label>性別</label>
                <div class="radio-group">
                    <!-- 性別 (gender) 1: 男性 -->
                    <label>
                        <input type="radio" name="gender" value="1" 
                               {{ (old('gender', $member->gender ?? '') == '1') ? 'checked' : '' }}> 男性
                    </label>

                    <!-- 性別 (gender) 2: 女性 -->
                    <label>
                        <input type="radio" name="gender" value="2" 
                               {{ (old('gender', $member->gender ?? '') == '2') ? 'checked' : '' }}> 女性
                    </label>
                </div>
                @error('gender')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-button">確認画面へ</button>
            
        </form>

      <!-- 「マイページに戻る」ボタンを中央に配置 -->
      <div class="back-to-mypage-container">
          <a href="{{ url('/mypage') }}" class="back-to-mypage-button">
              マイページに戻る
          </a>
      </div>


    </div>

    
</body>
</html>
