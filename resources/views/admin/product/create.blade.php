@include('admin.registrationEditCommon.productInput', [
    'pageTitle'    => '商品登録',
    'isEdit'       => false,
    'routePrefix'  => 'admin.product',
    'backLink'     => route('admin.product.index'),
    'product'      => null,
    // Controller から渡される配列をそのまま受け取る想定
    // 'input'      => $input ?? [],
    // 'members'    => $members ?? [],
    // 'categories' => $categories ?? [],
    // 'subcategories' => $subcategories ?? [],
])
