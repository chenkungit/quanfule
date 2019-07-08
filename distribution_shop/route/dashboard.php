<?php


/*-后台接口路由-*/
Route::group('dashboard', function () {
    Route::group('/auth', function () {
        Route::post('/login', 'dashboard/AuthController/login');
    });
    Route::group('/vip', function () {
        Route::group('/setting', function () {
            Route::get('/', 'dashboard/vip.SettingController/list');
            Route::post('/', 'dashboard/vip.SettingController/create');
            Route::put('/:id', 'dashboard/vip.SettingController/update');
        });
    });
    Route::group('/system', function () {
        Route::group('/setting', function () {
            Route::get('/', 'dashboard/system.SettingController/list');
            Route::put('/:id', 'dashboard/system.SettingController/update');
        });
    });
    Route::group('/treasure', function () {
        Route::group('/withdraw', function () {
            Route::get('/', 'dashboard/treasure.WithdrawController/list');
            Route::post('/finish', 'dashboard/treasure.WithdrawController/finish');
        });
        Route::group('/account_log', function () {
            Route::get('/', 'dashboard/treasure.AccountLogController/list');
        });
    });
    Route::group('/umbrella', function () {
        Route::group('/relation', function () {
            Route::get('/top_user', 'dashboard/umbrella.RelationController/topUser');
            Route::get('/down_user', 'dashboard/umbrella.RelationController/downUser');
        });
    });
    Route::group('/carousel', function () {
        Route::get('/lists', 'dashboard/CarouselController/lists');
        Route::post('/add', 'dashboard/CarouselController/add');
        Route::post('/edit', 'dashboard/CarouselController/edit');
        Route::delete('/delete', 'dashboard/CarouselController/delete');
    });
    Route::group('/nav', function () {
        Route::get('/lists', 'dashboard/NavController/lists');
        Route::post('/add', 'dashboard/NavController/add');
        Route::post('/edit', 'dashboard/NavController/edit');
        Route::delete('/delete', 'dashboard/NavController/delete');
    });
    Route::group('/hometheme', function () {
        Route::get('/lists', 'dashboard/HomethemeController/lists');
        Route::post('/add', 'dashboard/HomethemeController/add');
        Route::post('/edit', 'dashboard/HomethemeController/edit');
        Route::delete('/delete', 'dashboard/HomethemeController/delete');
    });
    Route::group('/category', function () {
        Route::get('/lists', 'dashboard/CategoryController/lists');
        Route::post('/add', 'dashboard/CategoryController/add');
        Route::post('/edit', 'dashboard/CategoryController/edit');
        Route::delete('/delete', 'dashboard/CategoryController/delete');
    });
    Route::group('/goodstype', function () {
        Route::get('/lists', 'dashboard/GoodstypeController/lists');
        Route::post('/add', 'dashboard/GoodstypeController/add');
        Route::post('/edit', 'dashboard/GoodstypeController/edit');
        Route::delete('/delete', 'dashboard/GoodstypeController/delete');
    });
    Route::group('/goods', function () {
        Route::get('/lists', 'dashboard/GoodsController/lists');
        Route::get('/info', 'dashboard/GoodsController/info');
        Route::delete('/delete', 'dashboard/GoodsController/delete');
        Route::post('/edit', 'dashboard/GoodsController/edit');
        Route::post('/update_sale', 'dashboard/GoodsController/update_sale');
        Route::post('/sup_insert', 'dashboard/GoodsController/sup_insert');
        Route::get('/sup_add', 'dashboard/GoodsController/sup_add');
        Route::get('/suppliers', 'dashboard/GoodsController/suppliers');
        Route::get('/goods_attr', 'dashboard/GoodsController/goods_attr');
        Route::get('/products', 'dashboard/GoodsController/products');
        Route::post('/products_add', 'dashboard/GoodsController/products_add');
        Route::post('/products_edit', 'dashboard/GoodsController/products_edit');
        Route::delete('/products_delete', 'dashboard/GoodsController/products_delete');
        Route::get('/product_list', 'dashboard/GoodsController/product_list');
        Route::get('/sup_edit', 'dashboard/GoodsController/sup_edit');
        Route::get('/sup_delete', 'dashboard/GoodsController/sup_delete');
        Route::get('/order', 'dashboard/GoodsController/order');
        Route::get('/cat_tree_list', 'dashboard/GoodsController/cat_tree_list');
    });
    Route::group('/goodsattr', function () {
        Route::get('/lists', 'dashboard/AttributeController/lists');
        Route::get('/selected', 'dashboard/AttributeController/selected');
        Route::get('/selected_list', 'dashboard/AttributeController/selected_list');
        Route::post('/add', 'dashboard/AttributeController/add');
        Route::post('/edit', 'dashboard/AttributeController/edit');
        Route::delete('/delete', 'dashboard/AttributeController/delete');
    });
    Route::group('/order', function () {
        Route::get('/lists', 'dashboard/OrderController/lists');
        Route::get('/info', 'dashboard/OrderController/info');
        Route::post('/asyncLogistics', 'dashboard/OrderController/asyncLogistics');
        Route::post('/fahuo', 'dashboard/OrderController/fahuo');
        Route::get('/logistics_type', 'dashboard/OrderController/logistics_type');
    });
    Route::group('/commentuser', function () {
        Route::get('/lists', 'dashboard/CommentuserController/lists');
        Route::get('/info', 'dashboard/CommentuserController/info');
        Route::post('/send', 'dashboard/CommentuserController/send');
        Route::post('/edit', 'dashboard/CommentuserController/edit');
        Route::put('/enable', 'dashboard/CommentuserController/enable');
    });
    Route::group('/privilege', function () {
        Route::get('/lists', 'dashboard/PrivilegeController/lists');
        Route::get('/allot', 'dashboard/PrivilegeController/allot_get');
        Route::put('/allot', 'dashboard/PrivilegeController/allot_put');
        Route::post('/add', 'dashboard/PrivilegeController/add');
        Route::post('/edit', 'dashboard/PrivilegeController/edit');
        Route::delete('/delete', 'dashboard/PrivilegeController/delete');
        Route::put('/repassword', 'dashboard/PrivilegeController/rePassword');
    });
    Route::group('/delivery', function () {
        Route::get('/lists', 'dashboard/DeliveryController/lists');
    });
    Route::group('/refund', function () {
        Route::get('/lists', 'dashboard/RefundController/lists');
        Route::get('/info', 'dashboard/RefundController/info');
        Route::post('/back', 'dashboard/RefundController/back');
        Route::post('/cancel_back', 'dashboard/RefundController/cancel_back');
    });
    Route::group('/member', function () {
        Route::get('/lists', 'dashboard/MemberController/lists');
        Route::get('/consume_list', 'dashboard/MemberController/consumeList');
        Route::post('/send_point', 'dashboard/MemberController/sendPoint');
        Route::get('/thankful_info', 'dashboard/MemberController/thankfulInfo');
        Route::post('/thankful_set', 'dashboard/MemberController/thankfulSet');
    });
    Route::group('/ajax', function () {
        Route::get('/search', 'dashboard/AjaxController/search');
        Route::get('/search_new', 'dashboard/AjaxController/search_new');
        Route::post('/upload', 'dashboard/AjaxController/upload');
        Route::get('/region', 'dashboard/AjaxController/region');
    });
    Route::post('/clearCache', 'dashboard/AjaxController/clearCache');

});