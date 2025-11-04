<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Hash;    
use Illuminate\Validation\Rule;

/**
 * 管理者向けの会員管理コントローラー
 * 会員一覧（検索/並べ替え）、新規登録、編集、削除機能を提供します。
 */
class MemberController extends Controller
{
    /**
     * 会員一覧、検索、並べ替え機能を処理する (管理者向け)
     * GET /admin/member
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ★修正点1: 会員登録・編集フローから外れた場合、セッションデータをクリアする
        // 新規登録用のセッションデータをクリア
        if (session()->has('admin.member.input')) {
            session()->forget('admin.member.input');
        }
        // 編集用のセッションデータもクリア (全ての編集セッションをクリア)
        $editSessionKeys = collect(session()->all())->keys()->filter(function ($key) {
            return str_starts_with($key, 'admin.member.edit.');
        });
        if ($editSessionKeys->isNotEmpty()) {
            session()->forget($editSessionKeys->toArray());
        }

        // 検索フォームの入力値を取得
        $searchParams = $request->only(['id', 'gender', 'freeword', 'sort_column', 'sort_direction']);

        // クエリビルダを初期化 (論理削除された会員を除外)
        // 必要に応じて $query = Member::whereNull('deleted_at'); などを使用
        $query = Member::query();

        // 1. ID検索 (完全に一致)
        if (!empty($searchParams['id'])) {
            $query->where('id', $searchParams['id']);
        }

        // 2. 性別検索 (複数選択された場合はOR検索)
        if (!empty($searchParams['gender']) && is_array($searchParams['gender'])) {
            $gendersToSearch = [];
            
            // ビューからの文字列をモデルの定数値にマッピング
            foreach ($searchParams['gender'] as $genderKey) {
                // Memberモデルに GENDER_MALE / GENDER_FEMALE 定数が定義されていることを前提とする
                if ($genderKey === 'male') {
                    // 定数が存在することを確認
                    $gendersToSearch[] = defined(Member::class . '::GENDER_MALE') ? Member::GENDER_MALE : 1;
                } elseif ($genderKey === 'female') {
                    // 定数が存在することを確認
                    $gendersToSearch[] = defined(Member::class . '::GENDER_FEMALE') ? Member::GENDER_FEMALE : 2;
                }
            }
            
            if (!empty($gendersToSearch)) {
                $query->whereIn('gender', $gendersToSearch);
            }
        }
        
        // 3. フリーワード検索 (氏名またはメールアドレスにOR部分一致)
        if (!empty($searchParams['freeword'])) {
            $freeword = $searchParams['freeword'];
            $query->where(function ($q) use ($freeword) {
                $q->where('name_sei', 'like', '%' . $freeword . '%')
                  ->orWhere('name_mei', 'like', '%' . $freeword . '%')
                  ->orWhere('email', 'like', '%' . $freeword . '%');
            });
        }


        // --- 並べ替え機能のロジック ---

        $sortColumn = $searchParams['sort_column'] ?? 'id';
        $sortDirection = $searchParams['sort_direction'] ?? 'desc'; // 初期表示はIDの降順

        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'id';
        }
        
        $direction = strtolower($sortDirection);
        if (!in_array($direction, ['asc', 'desc'])) {
            $sortDirection = 'desc'; // 不正な値はデフォルトに戻す
        } else {
            $sortDirection = $direction;
        }
        
        // クエリに並べ替えを適用
        $query->orderBy($sortColumn, $sortDirection);

        // --- ページネーションとデータ取得 ---

        // 1ページあたり10件表示
        $members = $query->paginate(10)->withQueryString();

        // ビューにデータを渡す
        return view('admin.index', [
            'members' => $members,
            'searchParams' => $searchParams,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }

    /** * 新規会員登録フォームの表示    
     * GET /admin/member/create 
     * * @return \Illuminate\View\View    
     */ 
    public function create()    
    {   
        // 入力中のデータをセッションから取得 (確認画面から戻った場合など)    
        $data = session()->get('admin.member.input', []);   
        // viewを共通入力ファイルを参照するように変更
        return view('admin.member.create', compact('data'));    
    }   

    /** * 新規会員登録 確認処理（カスタムルート） 
     * POST /admin/member/confirm   
     * * @param Request $request  
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View  
     */ 
    public function confirm(Request $request)   
    {   
        // ★修正点: パスワードのバリデーションルールを正規表現 regex:/^[a-zA-Z0-9]+$/ に変更
        $password_rule = 'required|regex:/^[a-zA-Z0-9]+$/|min:8|max:20|confirmed';
        $password_confirmation_rule = 'required|regex:/^[a-zA-Z0-9]+$/|min:8|max:20';

        $validatedData = $request->validate([   
            'name_sei' => 'required|string|max:20', 
            'name_mei' => 'required|string|max:20', 
            'nickname' => 'required|string|max:10', // 設計書では「必須」と記載
            'gender' => ['required', 'integer', Rule::in([1, 2])], // 1: 男性, 2: 女性  
            'email' => 'required|email|max:200|unique:members,email', // 200文字以内
            
            // 変更後のルール適用
            'password' => $password_rule, 
            'password_confirmation' => $password_confirmation_rule, 
        ], 
        [
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'gender.in' => '性別の値が不正です。男性または女性を選択してください。',
            // regex のカスタムメッセージを追加
            'password.regex' => 'パスワードは半角英数字のみで入力してください。',
            'password_confirmation.regex' => 'パスワード確認は半角英数字のみで入力してください。',
        ],
        // 第3引数: attributes配列でフィールド名を日本語化
        [
            'name_sei' => '氏名（姓）',
            'name_mei' => '氏名（名）',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'password_confirmation' => 'パスワード確認',
        ]);
        
        // 入力データをセッションに保存   
        session()->put('admin.member.input', $validatedData);   
        
        // viewを共通確認ファイルを参照するように変更し、validatedDataを渡す
        return view('admin.member.confirm', [
            'data' => $validatedData
        ]);    
    }   

    /** * 新規会員登録 完了処理（カスタムルート） 
     * POST /admin/member/complete  
     * * @param Request $request  
     * @return \Illuminate\Http\RedirectResponse
     */ 
    public function complete(Request $request)  
    {   
        // セッションから入力データを取得し、セッションをクリア   
        $data = session()->get('admin.member.input');   
        
        if (!$data) {   
            // データがない場合は入力フォームへリダイレクト   
            return redirect()->route('admin.member.create')
                            ->with('error', 'セッション切れまたは不正なアクセスです。再度入力してください。'); 
        }   
        
        try {   
            DB::transaction(function () use ($data) {   
                // パスワードをハッシュ化  
                $data['password'] = Hash::make($data['password']);  
                // パスワード確認用フィールドはDBに保存しないので削除
                unset($data['password_confirmation']);
                
                // 会員を登録    
                Member::create($data);  
            }); 
            
            // 登録完了後にセッションをクリア  
            session()->forget('admin.member.input');    
            
            // ★修正: 完了画面の代わりに会員一覧画面へリダイレクト
            return redirect()->route('admin.member.index')
                            ->with('success', '新規会員の登録が完了しました。');
            
        } catch (\Exception $e) {   
            // エラー処理    
            \Log::error('Admin Member Registration Error: ' . $e->getMessage());    
            return redirect()->route('admin.member.create') 
                            ->withInput($data) // エラー時に再入力できるようにデータを戻す  
                            ->with('error', '会員登録中にエラーが発生しました。');   
        }   
    }   

    /** * 会員編集フォームの表示  
     * GET /admin/member/{member}/edit  
     * * @param Member $member    
     * @return \Illuminate\View\View    
     */ 
    public function edit(Member $member)    
    {   
        // パスワードを除外した初期データ
        $initialData = $member->toArray();  
        unset($initialData['password']);    
        
        // 編集中のデータをセッションから取得 (確認画面から戻った場合など)    
        // フォームでold()を使うため、セッションデータをold()にコピーする処理を想定
        $data = session()->get('admin.member.edit.' . $member->id, $initialData);   
        
        // viewを共通入力ファイルを参照するように変更
        return view('admin.member.edit', compact('member', 'data'));    
    }   

    /** * 会員編集 確認処理（カスタムルート）   
     * PUT/PATCH /admin/member/{member}/confirm 
     * * @param Request $request  
     * @param Member $member    
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View  
     */ 
    public function updateConfirm(Request $request, Member $member) 
    {   
        // ★修正点: パスワードのバリデーションルールを正規表現 regex:/^[a-zA-Z0-9]+$/ に変更
        $password_rule_base = 'regex:/^[a-zA-Z0-9]+$/|min:8|max:20';

        $validatedData = $request->validate([   
            'name_sei' => 'required|string|max:20', 
            'name_mei' => 'required|string|max:20', 
            'nickname' => 'required|string|max:10', 
            'gender' => ['required', 'integer', Rule::in([1, 2])], // 1: 男性, 2: 女性  
            // メールアドレスは自分自身を除く
            'email' => ['required', 'email', 'max:200', Rule::unique('members')->ignore($member->id)],  
            
            // 変更後のルール適用
            'password' => 'nullable|required_with:password_confirmation|' . $password_rule_base . '|confirmed',    
            'password_confirmation' => 'nullable|required_with:password|' . $password_rule_base,
        ], 
        [
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'password.required_with' => 'パスワード確認欄に入力する場合は、パスワードも入力してください。',
            'password_confirmation.required_with' => 'パスワード欄に入力する場合は、パスワード確認も入力してください。',
            'gender.in' => '性別の値が不正です。男性または女性を選択してください。',
            // regex のカスタムメッセージを追加
            'password.regex' => 'パスワードは半角英数字のみで入力してください。',
            'password_confirmation.regex' => 'パスワード確認は半角英数字のみで入力してください。',
        ],
        // 第3引数: attributes配列でフィールド名を日本語化
        [
            'name_sei' => '氏名（姓）',
            'name_mei' => '氏名（名）',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'password_confirmation' => 'パスワード確認',
        ]); 
        
        // 会員IDをキーとしてセッションに保存   
        session()->put('admin.member.edit.' . $member->id, $validatedData); 
        
        // viewを共通確認ファイルを参照するように変更
        return view('admin.member.update_confirm', [    
            'member' => $member, // 元データ    
            'data' => $validatedData, // 更新データ  
        ]); 
    }   

    /** * 会員編集 完了処理（カスタムルート）   
     * PUT/PATCH /admin/member/{member}/complete    
     * * @param Request $request  
     * @param Member $member    
     * @return \Illuminate\Http\RedirectResponse
     */ 
    public function updateComplete(Request $request, Member $member)    
    {   
        // 会員IDをキーとしてセッションから更新データを取得    
        $data = session()->get('admin.member.edit.' . $member->id); 
        
        if (!$data) {   
            return redirect()->route('admin.member.edit', $member)  
                            ->with('error', 'セッション切れまたは不正なアクセスです。再度入力してください。'); 
        }   
        
        // トランザクション処理   
        try {   
            DB::transaction(function () use ($member, $data) {  
                
                // パスワードが入力されていればハッシュ化して更新データに含める
                if (!empty($data['password'])) {    
                    $data['password'] = Hash::make($data['password']);  
                } else {    
                    // パスワードが入力されていない場合は更新データから除外
                    unset($data['password']);   
                }
                
                // パスワード確認用フィールドはDBに保存しないので削除
                unset($data['password_confirmation']);
                
                // 会員情報を更新  
                $member->update($data); 
            }); 
            
            // 更新完了後にセッションをクリア  
            session()->forget('admin.member.edit.' . $member->id);  
            
            // ★修正: 完了画面の代わりに会員一覧画面へリダイレクト
            return redirect()->route('admin.member.index')
                            ->with('success', '会員（ID: ' . $member->id . '）の情報を更新しました。');
            
        } catch (\Exception $e) {   
            \Log::error('Admin Member Update Error: ' . $e->getMessage());  
            return redirect()->route('admin.member.edit', $member)  
                            ->withInput($data)  
                            ->with('error', '会員情報更新中にエラーが発生しました。'); 
        }   
    }   

    /** * 会員削除処理   
     * DELETE /admin/member/{member}    
     * * @param Member $member    
     * @return \Illuminate\Http\RedirectResponse    
     */ 
    public function destroy(Member $member) 
    {   
        try {   
            // 削除実行 (SoftDeletesが有効であることを想定)    
            $member->delete();  
            
            return redirect()->route('admin.member.index')  
                            ->with('success', '会員（ID: ' . $member->id . '）を削除しました。');
        } catch (\Exception $e) {   
            \Log::error('Admin Member Deletion Error: ' . $e->getMessage());    
            return redirect()->route('admin.member.index')  
                            ->with('error', '会員削除中にエラーが発生しました。');   
        }   
    }
}
