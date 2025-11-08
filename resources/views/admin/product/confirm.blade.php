@include('admin.registrationEditCommon.productConfirm', [
    'pageTitle'   => '商品登録確認',
    'routePrefix' => 'admin.product',
    'isEdit'      => false,
    'input'       => $input ?? [],
    'product'     => null,
])
