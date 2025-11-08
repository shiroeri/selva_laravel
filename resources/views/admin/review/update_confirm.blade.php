@include('admin.registrationEditCommon.reviewConfirm', [
    'pageTitle' => '商品レビュー編集確認',
    'isEdit'    => true,
    'review'    => $review,
    'input'     => $input,
    'product'   => $product,
    'member'    => $member,
    'ratingAvgCeil' => $ratingAvgCeil,
])
