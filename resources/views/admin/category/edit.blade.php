@php
// 編集用の設定
$pageTitle = '商品カテゴリ編集';
$isEdit = true;
$routePrefix = 'admin.category';

// 一覧画面へのリンクを設定（必要に応じてルート名を変更してください）
$backLink = route('admin.category.index');

// コントローラーから渡される変数:
// $category: データベースから取得した ProductCategory インスタンス (必須)
// $input: 確認画面から戻った際のセッションデータ（古い入力値）

// 既存のカテゴリデータをフォームの初期値として整形
// old()または $input が優先されるため、ここでは $input に整形したデータをセットします。
// $input はセッションから戻ってきたデータがある場合にのみ使用し、通常時は $category のデータを使用します。
if (!isset($input) || empty($input)) {
    // DBデータから初期値を作成
    $categoryData = [
        'category_name' => $category->name,
        'subcategories' => [], // 初期化
    ];

    // 登録されている小カテゴリを抽出
    // $category->subcategories がリレーションなどで取得できる配列またはコレクションだと仮定します。
    // リレーションをロードしていない場合は、コントローラー側で with('subcategories') が必要です。
    $existingSubcategories = $category->subcategories ?? [];

    // 小カテゴリを最大10個まで配列に格納
    foreach ($existingSubcategories as $subcat) {
        // $subcat がオブジェクトの場合、'name'属性を取得
        // $subcat が文字列の場合、そのまま使用
        $name = is_object($subcat) && isset($subcat->name) ? $subcat->name : (is_string($subcat) ? $subcat : '');
        if (!empty($name)) {
             $categoryData['subcategories'][] = $name;
        }
    }

    // 残りの配列要素を空文字で埋めて10個にする
    while (count($categoryData['subcategories']) < 10) {
        $categoryData['subcategories'][] = '';
    }

    $input = $categoryData;
}

// $input が確定したら、共通ファイルに渡す
$input = $input ?? [];


@endphp

{{-- 共通テンプレートを読み込み、変数を渡す --}}
@include('admin.registrationEditCommon.categoryInput', [
'pageTitle' => $pageTitle,
'isEdit' => $isEdit,
'routePrefix' => $routePrefix,
'input' => $input, // 既存のデータまたは確認画面からのデータ
'category' => $category, // ID表示のために Category インスタンスそのものも渡す
'backLink' => $backLink,
])