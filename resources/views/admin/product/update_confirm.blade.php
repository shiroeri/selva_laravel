@include('admin.registrationEditCommon.productConfirm', [
    'pageTitle'   => '商品編集確認',
    'routePrefix' => 'admin.product',
    'isEdit'      => true,
    'input'       => $input ?? [],
    'product'     => $product ?? null,
])
