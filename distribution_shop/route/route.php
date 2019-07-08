<?php


Route::any('/', function () {
    return 'hello world';
});
Route::post('/webhook', 'web/AsyncController/webHook');


Route::miss(function () {
    abort(405, 'Method Not Allowed');
});
