@include('admin.registrationEditCommon.reviewConfirm', [
    'pageTitle' => '商品レビュー登録確認',
    'isEdit'    => false,
    'review'    => null,
    'input'     => $input,
    'product'   => $product,
    'member'    => $member,
    'ratingAvgCeil' => $ratingAvgCeil,
])
