<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; 
use Illuminate\Validation\ValidationException; 
use Illuminate\Support\Facades\Validator; 

class MemberEditController extends Controller
{
    /**
     * バリデーションルールを定義します。
     * @return array
     */
    private function getValidationRules()
    {
        return [
            'name_sei' => 'required|string|max:20',
            'name_mei' => 'required|string|max:20',
            'nickname' => 'required|string|max:10',
            'gender' => 'required|in:1,2', // 1:男性, 2:女性
        ];
    }
    
    /**
     * バリデーションエラーメッセージに使用する日本語属性名を定義します。
     * @return array
     */
    private function getValidationAttributes()
    {
        return [
            'name_sei' => '姓',
            'name_mei' => '名',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
        ];
    }
    
    /**
     * カスタムバリデーションメッセージを定義します。
     * @return array
     */
    private function getValidationMessages() // <-- 【新規追加】
    {
        return [
            // genderフィールドのinルール（選択肢外の値）に対するメッセージ
            'gender.in' => ':attribute の値が不正です。',
        
            // 必要に応じて他のルールに対するカスタムメッセージもここに追加できます
            // 'name_sei.required' => '氏名の姓は必ず入力してください。',
        ];
    }

    /**
     * 1. フォーム表示 (MemberChangeForm.blade.php)
     * ログインユーザーの情報をフォームの初期値として表示します。
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function form(Request $request)
    {
        // ログインユーザー情報を取得
        $member = Auth::user();

        // セッションにフォーム入力値が残っていればそれを優先（「前に戻る」処理）
        $inputData = $request->session()->get('member_edit_input', []);

        // セッションデータとDBデータをマージ（セッションデータが優先）
        $memberData = array_merge($member->toArray(), $inputData);
        // Memberモデルオブジェクトとしてビューに渡す
        $member = new Member($memberData);

        // ビューは 'member.MemberChangeForm' を使用します
        return view('member.MemberChangeForm', compact('member'));
    }

    /**
     * 2. 確認画面表示 (MemberChangeConfirm.blade.php)
     * フォームの入力値をバリデーションし、セッションに一時保存後、確認画面へ遷移します。
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function confirm(Request $request)
    {
        // Validator::make() の第3引数にカスタムメッセージを追加
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages(), // <-- 【適用】カスタムメッセージを渡す
            $this->getValidationAttributes()
        );

        // バリデーション実行
        if ($validator->fails()) {
            // バリデーションエラー時はフォームに戻り、エラーメッセージと入力値がセッションに格納されます。
            throw new ValidationException($validator);
        }

        // バリデーション通過後の処理
        $validatedData = $validator->validated();

        // バリデーション通過後、入力値をセッションに一時保存（別のキーを使用）
        $request->session()->put('member_edit_input', $validatedData);

        // 確認画面にデータを渡して表示
        // ビューは 'member.MemberChangeConfirm' を使用します
        return view('member.MemberChangeConfirm', ['data' => $validatedData]);
    }

    /**
     * 3. DB更新処理 (更新とセッションクリア)
     * セッションからデータを取得し、ログインユーザーの情報を更新します。
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // セッションから入力データを取得
        $data = $request->session()->get('member_edit_input');

        // セッションデータがない場合は不正な遷移とみなし、フォームに戻す
        if (!$data) {
            return redirect()->route('member.edit.form')->with('error', '不正な遷移またはセッションが切れました。最初から入力してください。');
        }

        // ログインユーザー情報を取得
        $member = Auth::user();

        if (!$member) {
            $request->session()->forget('member_edit_input');
            return redirect('/login')->with('error', '認証情報が見つかりません。再ログインしてください。');
        }

        // DBに保存（更新）
        try {
            $member->update([
                'name_sei' => $data['name_sei'],
                'name_mei' => $data['name_mei'],
                'nickname' => $data['nickname'],
                'gender' => $data['gender'],
            ]);

            // セッションデータをクリア
            $request->session()->forget('member_edit_input');

            // 完了画面へのアクセスを許可するフラグを設定
            $request->session()->put('member_edit_complete', true);

            // 完了画面へリダイレクト
            return redirect()->route('member.edit.complete');

        } catch (\Exception $e) {
            // DB更新失敗時の処理
            $request->session()->forget('member_edit_input');
            // エラーをログに出力
            \Illuminate\Support\Facades\Log::error('Member update failed: ' . $e->getMessage());
            return redirect()->route('member.edit.form')->with('error', '情報の更新に失敗しました。時間をおいて再度お試しください。');
        }
    }

    /**
     * 4. 完了画面表示
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(Request $request)
    {
        // 完了フラグがない場合は不正アクセスとみなし、フォームに戻す
        if (!$request->session()->pull('member_edit_complete')) {
            return redirect()->route('member.edit.form')->with('error', '不正なアクセスです。');
        }

        // 完了画面のビューを作成していない場合、マイページなどへリダイレクトします
        // 実際の完了ビューを表示する場合は、return view('member.MemberChangeComplete'); のようにします
        return redirect('/mypage')->with('status', '会員情報を変更しました。');
    }
}
