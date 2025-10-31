<?php

namespace App\Http\Requests\Mypage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReviewRequest extends FormRequest
{
    /**
     * リクエストがこのアクションを承認されているか判断する。
     * ログインユーザーが自分のレビューを編集しようとしているかを確認します。
     *
     * @return bool
     */
    public function authorize()
    {
        // 認証済みのユーザーであること
        if (!Auth::check()) {
            return false;
        }

        // URLパラメータからProductReviewモデルを取得（route()ヘルパーにより自動でバインドされる）
        // ただし、この確認はControllerのconfirmメソッド内でも行っているため、ここではシンプルにtrueでも可。
        // セキュリティを高めるために、一応チェックを入れます。
        $review = $this->route('review'); // URLの変数名が 'review' であると仮定
        if ($review && $review->member_id !== Auth::id()) {
            return false;
        }

        return true;
    }

    /**
     * リクエストに適用するバリデーションルールを取得する。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            // 商品評価は1～5の整数で必須
            'review_evaluation' => ['required', 'integer', 'min:1', 'max:5'], 
            // レビューコメントは必須で最大500文字
            'review_comment' => ['required', 'string', 'max:500'],        
        ];
    }

    /**
     * バリデーションエラーメッセージをカスタマイズする。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'review_evaluation.required' => '商品評価は必須です。',
            'review_evaluation.integer' => '商品評価は整数で入力してください。',
            'review_evaluation.min' => '商品評価は1以上を選択してください。',
            'review_evaluation.max' => '商品評価は5以下を選択してください。',
            'review_comment.required' => 'レビューコメントは必須です。',
            'review_comment.string' => 'レビューコメントは文字列で入力してください。',
            'review_comment.max' => 'レビューコメントは500文字以内で入力してください。',
        ];
    }
    
    /**
     * バリデーションエラーメッセージで使用される属性名をカスタマイズする。
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'review_evaluation' => '商品評価',
            'review_comment' => 'レビューコメント',
        ];
    }
}
