<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Member;

class ReviewController extends Controller
{
    /**
     * 商品レビュー一覧（検索・並べ替え・ページャ）
     * GET /admin/review
     */
    public function index(Request $request): View
    {
        // 受け取る検索・並べ替えパラメータ
        $searchParams  = $request->only(['id', 'keyword']);
        $sortColumn    = $request->get('sort_column', 'id');      // 初期：id
        $sortDirection = $request->get('sort_direction', 'desc'); // 初期：降順

        // 並べ替え許可カラムを限定
        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns, true)) {
            $sortColumn = 'id';
        }
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        // クエリ作成（Eloquent）
        $query = ProductReview::query(); // reviews テーブル

        // ── 検索（縦：AND）────────────────────────────
        // ID：完全一致
        if (filled($searchParams['id'] ?? null)) {
            $query->where('reviews.id', $searchParams['id']);
        }

        // フリーワード：comment に対して「横：OR」。空白区切りで複数語を OR。
        if (filled($searchParams['keyword'] ?? null)) {
            // 全角/半角スペースを統一して分割
            $kw = preg_replace('/\x{3000}/u', ' ', $searchParams['keyword']); // 全角→半角
            $terms = array_values(array_filter(array_map('trim', explode(' ', $kw)), fn($v) => $v !== ''));

            if (!empty($terms)) {
                $query->where(function ($q) use ($terms) {
                    foreach ($terms as $i => $t) {
                        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $t) . '%';
                        if ($i === 0) {
                            $q->where('reviews.comment', 'like', $like);
                        } else {
                            $q->orWhere('reviews.comment', 'like', $like);
                        }
                    }
                });
            }
        }

        // 並べ替え
        $query->orderBy($sortColumn, $sortDirection);

        // 1ページ10件
        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.review.index', [
            'reviews'       => $reviews,
            'searchParams'  => $searchParams,
            'sortColumn'    => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }

    /** 登録フォーム */
    public function create(Request $request)
    {
        $input = $request->session()->get('admin.review.input', []);

        return view('admin.review.create', [
            'input'   => $input,
            'products'=> Product::orderBy('id','desc')->get(['id','name','image_1']),
            'members' => Member::orderBy('id','desc')->get(['id','name_sei','name_mei']),
        ]);
    }

    /** 登録 確認 */
    public function confirm(Request $request)
    {
        $validated = $this->validateReview($request);

        // セッションに保存（戻る時のため）
        $request->session()->put('admin.review.input', $validated);

        // 確認画面で表示する商品情報＆総合評価
        $product = Product::find($validated['product_id']);
        $member  = Member::find($validated['member_id']);

        $avg    = ProductReview::where('product_id', $validated['product_id'])->avg('evaluation');
        $ratingAvgCeil = $avg ? (int)ceil($avg) : 0;

        return view('admin.review.confirm', [
            'isEdit'         => false,
            'review'         => null,
            'input'          => $validated,
            'product'        => $product,
            'member'         => $member,
            'ratingAvgCeil'  => $ratingAvgCeil,
        ]);
    }

    /** 登録 完了 */
    public function store(Request $request)
    {
        $input = $request->session()->get('admin.review.input');
        if (!$input) {
            return redirect()->route('admin.review.create')->with('error', 'セッションが切れました。もう一度入力してください。');
        }

        ProductReview::create([
            'product_id' => $input['product_id'],
            'member_id'  => $input['member_id'],
            'evaluation' => $input['evaluation'],
            'comment'    => $input['comment'],
        ]);

        $request->session()->forget('admin.review.input');

        return redirect()->route('admin.review.index')->with('success', '商品レビューを登録しました。');
    }

    /** 編集フォーム */
    public function edit(Request $request, ProductReview $review)
    {
        $sessionKey = "admin.review.edit.{$review->id}";
        $input = $request->session()->get($sessionKey, [
            'id'         => $review->id,
            'product_id' => $review->product_id,
            'member_id'  => $review->member_id,
            'evaluation' => $review->evaluation,
            'comment'    => $review->comment,
        ]);

        return view('admin.review.edit', [
            'review'  => $review,
            'input'   => $input,
            'products'=> Product::orderBy('id','desc')->get(['id','name','image_1']),
            'members' => Member::orderBy('id','desc')->get(['id','name_sei','name_mei']),
        ]);
    }

    /** 編集 確認（PUT/PATCH） */
    public function updateConfirm(Request $request, ProductReview $review)
    {
        $validated = $this->validateReview($request, $review->id);

        $sessionKey = "admin.review.edit.{$review->id}";
        $request->session()->put($sessionKey, array_merge(['id'=>$review->id], $validated));

        $product = Product::find($validated['product_id']);
        $member  = Member::find($validated['member_id']);

        $avg    = ProductReview::where('product_id', $validated['product_id'])->avg('evaluation');
        $ratingAvgCeil = $avg ? (int)ceil($avg) : 0;

        return view('admin.review.update_confirm', [
            'isEdit'         => true,
            'review'         => $review,
            'input'          => $validated,
            'product'        => $product,
            'member'         => $member,
            'ratingAvgCeil'  => $ratingAvgCeil,
        ]);
    }

    /** 編集 確認（GET：戻る用） */
    public function showUpdateConfirm(Request $request, ProductReview $review)
    {
        $sessionKey = "admin.review.edit.{$review->id}";
        $input = $request->session()->get($sessionKey);

        if (!$input) {
            return redirect()->route('admin.review.edit', $review->id)->with('error', '確認セッションが見つかりませんでした。');
        }

        $product = Product::find($input['product_id']);
        $member  = Member::find($input['member_id']);
        $avg    = ProductReview::where('product_id', $input['product_id'])->avg('evaluation');
        $ratingAvgCeil = $avg ? (int)ceil($avg) : 0;

        return view('admin.review.update_confirm', [
            'isEdit'         => true,
            'review'         => $review,
            'input'          => $input,
            'product'        => $product,
            'member'         => $member,
            'ratingAvgCeil'  => $ratingAvgCeil,
        ]);
    }

    /** 編集 完了 */
    public function update(Request $request, ProductReview $review)
    {
        $sessionKey = "admin.review.edit.{$review->id}";
        $input = $request->session()->get($sessionKey);

        if (!$input) {
            return redirect()->route('admin.review.edit', $review->id)->with('error', 'セッションが切れました。もう一度入力してください。');
        }

        $review->update([
            'product_id' => $input['product_id'],
            'member_id'  => $input['member_id'],
            'evaluation' => $input['evaluation'],
            'comment'    => $input['comment'],
        ]);

        $request->session()->forget($sessionKey);

        return redirect()->route('admin.review.index')->with('success', '商品レビューを更新しました。');
    }

    /* ==========
       共通: バリデーション
       ========== */
    private function validateReview(Request $request, ?int $editingId = null): array
    {
        $rules = [
            'product_id' => ['required','integer', Rule::exists('products','id')],
            'member_id'  => ['required','integer', Rule::exists('members','id')],
            'evaluation' => ['required','integer','between:1,5'],
            'comment'    => ['required','string','max:500'],
        ];
        $attrs = [
            'product_id' => '商品',
            'member_id'  => '会員',
            'evaluation' => '商品評価',
            'comment'    => '商品コメント',
        ];
        $messages = [
            'required' => ':attribute は必須です。',
            'integer'  => ':attribute は数値で入力してください。',
            'between'  => ':attribute は :min 〜 :max の範囲で入力してください。',
            'max'      => ':attribute は :max 文字以内で入力してください。',
            'exists'   => '選択された :attribute は存在しません。',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attrs);

        if ($validator->fails()) {
            // 編集か新規かで戻り先を分ける
            if ($editingId) {
                return redirect()->route('admin.review.edit', $editingId)
                    ->withErrors($validator)->withInput()->throwResponse();
            } else {
                return redirect()->route('admin.review.create')
                    ->withErrors($validator)->withInput()->throwResponse();
            }
        }

        return $validator->validated();
    }

    // 商品レビュー詳細
    public function show(\App\Models\ProductReview $review): \Illuminate\View\View
    {
        // 関連
        $product = $review->product;           // ProductReview::product()
        $member  = $review->member;            // ProductReview::member()

        // 商品画像（1枚目を表示）
        $imageUrl = null;
        if ($product && $product->image_1) {
            $imageUrl = asset('storage/' . ltrim($product->image_1, '/'));
        }

        // 総合評価（その商品のレビュー平均を切り上げ）
        $ratingAvg = \App\Models\ProductReview::where('product_id', $product?->id)->avg('evaluation');
        $ratingAvgCeil = $ratingAvg ? (int)ceil($ratingAvg) : 0;
        $ratingCount   = \App\Models\ProductReview::where('product_id', $product?->id)->count();

        return view('admin.review.show', [
            'review'         => $review,
            'product'        => $product,
            'member'         => $member,
            'imageUrl'       => $imageUrl,
            'ratingAvgCeil'  => $ratingAvgCeil,
            'ratingCount'    => $ratingCount,
        ]);
    }

    // 商品レビュー削除（ソフトデリート）
    public function destroy(\App\Models\ProductReview $review): \Illuminate\Http\RedirectResponse
    {
        $review->delete(); // SoftDeletes
        return redirect()
            ->route('admin.review.index')
            ->with('success', '商品レビューを削除しました。');
    }

}
