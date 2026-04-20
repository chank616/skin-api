<?php

use Blessing\Filter;
use Illuminate\Contracts\Events\Dispatcher;
use App\Services\Hook;
use SkinApiExtension\Middlewares\TokenAuth;


return function (Dispatcher $events, Filter $filter) {
    Hook::addRoute(function ($routes) {
        $routes->post('ext/get-uids-by-suffix', 'SkinApiExtension\GjxController@getUidBySuffix')->middleware(TokenAuth::class);
        $routes->post('ext/get-avatar-by-uid', 'SkinApiExtension\GjxController@getAvatarByUid')->middleware(TokenAuth::class);
    });
};
