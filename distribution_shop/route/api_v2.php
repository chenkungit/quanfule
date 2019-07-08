<?php
Route::group('v2', function () {
    Route::group('/home', function () {
        Route::get('/index', 'api/v2.HomeController/index');
    });
    Route::group('/order', function () {
        Route::post('/checkout', 'api/v2.OrderController/checkout_info');
        Route::post('/refund', 'api/v2.OrderController/refund');
    });
    Route::group('/user', function () {
        Route::post('/modify_password', 'api/v2.UserController/modify_password');
    });
    Route::group('/pay', function () {
        Route::post('/done', 'api/v2.PaymentController/done');
    });
    Route::group('/sms', function () {
        Route::post('/captcha', 'api/v2.SmsController/captcha');
        Route::post('/send', 'api/v2.SmsController/send');
    });
});