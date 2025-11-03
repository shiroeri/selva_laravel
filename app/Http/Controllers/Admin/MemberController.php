<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Routing\Controller;

class MemberController extends Controller
{
    /**
     * 会員一覧、検索、並べ替え機能を処理する (管理者向け)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 検索フォームの入力値を取得
        // 'sort_column' と 'sort_direction' もクエリパラメータとして取得
        $searchParams = $request->only(['id', 'gender', 'freeword', 'sort_column', 'sort_direction']);

        // クエリビルダを初期化 (論理削除された会員を除外)
        // 必要に応じて $query = Member::whereNull('deleted_at'); などを使用
        $query = Member::query();

        // --- 縦の組み合わせ検索: AND条件 (各条件を $query に独立して適用) ---

        // 1. ID検索 (縦検索: IDは完全に一致するAND条件)
        if (!empty($searchParams['id'])) {
            $query->where('id', $searchParams['id']);
        }

        // 2. 性別検索 (横検索: 複数選択された場合はOR検索)
        if (!empty($searchParams['gender']) && is_array($searchParams['gender'])) {
            $gendersToSearch = [];
            
            // 【重要：修正点】ビューから受け取った文字列をモデルの定数値にマッピング
            foreach ($searchParams['gender'] as $genderKey) {
                // Memberモデルに GENDER_MALE / GENDER_FEMALE 定数が定義されていることを前提とする
                if ($genderKey === 'male') {
                    $gendersToSearch[] = Member::GENDER_MALE;
                } elseif ($genderKey === 'female') {
                    $gendersToSearch[] = Member::GENDER_FEMALE;
                }
            }
            
            if (!empty($gendersToSearch)) {
                // whereIn() を使用することで、データベースの定数値に対して OR 条件が適用されます
                $query->whereIn('gender', $gendersToSearch);
            }
        }
        
        // 3. フリーワード検索 (縦検索: 氏名やメールアドレスのいずれかに部分一致するAND条件)
        // フリーワード自体が、複数のカラムに対してOR検索を行います。
        if (!empty($searchParams['freeword'])) {
            $freeword = $searchParams['freeword'];
            $query->where(function ($q) use ($freeword) {
                // 氏名（姓）に部分一致 (OR)
                $q->where('name_sei', 'like', '%' . $freeword . '%')
                  // 氏名（名）に部分一致 (OR)
                  ->orWhere('name_mei', 'like', '%' . $freeword . '%')
                  // メールアドレスに部分一致 (OR)
                  ->orWhere('email', 'like', '%' . $freeword . '%');
            });
        }


        // --- 並べ替え機能のロジック ---
        // 要件: 会員一覧の初期表示はIDの降順になっているか

        $sortColumn = $searchParams['sort_column'] ?? 'id';
        // 修正点: 初期表示を降順 'desc' に設定
        $sortDirection = $searchParams['sort_direction'] ?? 'desc';

        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'id';
        }
        
        // 小文字化してチェック
        $direction = strtolower($sortDirection);
        if (!in_array($direction, ['asc', 'desc'])) {
            $sortDirection = 'desc'; // 不正な値は初期表示のデフォルトに戻す
        } else {
            $sortDirection = $direction; // チェックを通過した値を設定
        }
        
        // クエリに並べ替えを適用
        $query->orderBy($sortColumn, $sortDirection);

        // --- ページネーションとデータ取得 ---

        // 1ページあたり10件表示
        // withQueryString()で検索・並び替えパラメータを自動でページネーションリンクに含めます
        $members = $query->paginate(10)->withQueryString();

        // ビューにデータを渡す
        return view('admin.index', [
            'members' => $members,
            'searchParams' => $searchParams,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }
}
