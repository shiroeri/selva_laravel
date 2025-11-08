@include('admin.registrationEditCommon.productInput', [
    'pageTitle'    => '商品編集',
    'isEdit'       => true,
    'routePrefix'  => 'admin.product',
    'backLink'     => route('admin.product.index'),
    'product'      => $product,       // 必須
    // 'input'      => $input ?? [],
    // 'members'    => $members ?? [],
    // 'categories' => $categories ?? [],
    // 'subcategories' => $subcategories ?? [],
])
