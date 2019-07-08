<?php


Route::group('v1', function () {
    Route::group('/captcha', function () {
        Route::get('/', 'api/v1.CaptchaController/create');
    });
    Route::group('/passport', function () {
        Route::group('/auth', function () {
            Route::post('/sign_up', 'api/v1.passport.AuthController/signUp');
        });
    });
    Route::group('/member', function () {
        Route::group('/card', function () {
            Route::get('/', 'api/v1.member.CardController/list');
            Route::post('/', 'api/v1.member.CardController/create');
            Route::post('/check', 'api/v1.member.CardController/check');
            Route::get('/:id', 'api/v1.member.CardController/info');
            Route::put('/:id', 'api/v1.member.CardController/update');
            Route::delete('/:id', 'api/v1.member.CardController/delete');
        });
        Route::group('/qrcode', function () {
            Route::get('/share', 'api/v1.member.QrCodeController/share');
            Route::post('/relate', 'api/v1.member.QrCodeController/relate');
        });
        Route::group('/treasure', function () {
            Route::get('/info', 'api/v1.member.TreasureController/info');
            Route::post('/convert', 'api/v1.member.TreasureController/convert');
            Route::get('/flow', 'api/v1.member.TreasureController/flowList');
            Route::get('/transfer', 'api/v1.member.TreasureController/transferList');
            Route::post('/transfer', 'api/v1.member.TreasureController/transfer');
            Route::post('/withdraw_apply', 'api/v1.member.TreasureController/withdrawApply');
            Route::get('/withdraw_apply', 'api/v1.member.TreasureController/withdrawApplyList');
        });
    });
    Route::group('/distribution', function () {
        Route::get('/x', 'api/v1.distribution.TestController/x');
    });
    Route::group('/umbrella', function () {
        Route::group('/relation', function () {
            Route::get('/get_down_user', 'api/v1.umbrella.RelationController/getDownUser');
        });
    });
    Route::group('/wechat', function () {
        Route::group('/server', function () {
            Route::any('/', 'api/v1.wechat.ServerController/server');
        });
        Route::group('/oauth', function () {
            Route::any('/authorize', 'api/v1.wechat.OauthController/authorize');
            Route::any('/callback', 'api/v1.wechat.OauthController/callback');
        });
    });
    Route::group('/category', function () {
        Route::get('/parent_list', 'api/v1.CategoryController/parent_list');
        Route::get('/product_list', 'api/v1.CategoryController/product_list');
    });
    Route::group('/goods', function () {
        Route::get('/info', 'api/v1.GoodsController/info');
        Route::any('/price', 'api/v1.GoodsController/price');
    });
    Route::group('/cart', function () {
        Route::get('/lists', 'api/v1.CartController/lists');
        Route::post('/add', 'api/v1.CartController/add');
        Route::put('/edit', 'api/v1.CartController/edit');
        Route::delete('/delete', 'api/v1.CartController/delete');
    });
    Route::group('/order', function () {
        Route::get('/list', 'api/v1.OrderController/order_list');
        Route::get('/logistics_type', 'api/v1.OrderController/logistics_type');
        Route::get('/detail', 'api/v1.OrderController/order_detail');
        Route::get('/tracking', 'api/v1.OrderController/tracking');
        Route::put('/close', 'api/v1.OrderController/close');
        Route::put('/cancel_refund', 'api/v1.OrderController/cancel_refund');
        Route::put('/affirm_received', 'api/v1.OrderController/affirm_received');

    });
    Route::group('/pay', function () {
        Route::post('/done', 'api/v1.PaymentController/done');
    });
    Route::group('/notify', function () {
        Route::any('/wechatjs_shop', 'api/v1.NotifyController/wechatJs_shop');
    });
    Route::group('/address', function () {
        Route::get('/lists', 'api/v1.AddressController/lists');
        Route::get('/info', 'api/v1.AddressController/info');
        Route::post('/add', 'api/v1.AddressController/add');
        Route::put('/edit', 'api/v1.AddressController/edit');
        Route::delete('/delete', 'api/v1.AddressController/delete');
        Route::post('/regions', 'api/v1.AddressController/get_regions');
    });
    Route::group('/auth', function () {
        Route::post('/signup_p', 'api/v1.AuthController/signup_p');
        Route::post('/signup', 'api/v1.AuthController/signup');
        Route::post('/signin', 'api/v1.AuthController/signin');
        Route::post('/forget', 'api/v1.AuthController/forget');
        Route::delete('/logout', 'api/v1.AuthController/logout');
    });
    Route::group('/user', function () {
        Route::get('/info', 'api/v1.UserController/info');
        Route::post('/info_edit', 'api/v1.UserController/info_edit');
    });
    Route::group('/Search', function () {
        Route::get('/sou', 'api/v1.SearchController/sou');
        Route::get('/prompt', 'api/v1.SearchController/prompt');
        Route::get('/hot', 'api/v1.SearchController/hot');
    });
    Route::post('/upload', 'api/v1.UserController/upload');
});