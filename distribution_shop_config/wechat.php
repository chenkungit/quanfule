<?php

return [
    'easywechat_config' => [
        /*** 当值为 false 时，所有的日志都不会记录 */
        'debug' => false,
        'app_id' => 'wx6601e34e3e50ed65',         // AppID
        'secret' => 'c03a3b769d12acba5f5c7f67bb11c3e7',     // AppSecret
        'token' => 'distribution',          // Token
        'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

        'response_type' => 'array',
        /**
         * 接口请求相关配置，超时时间等，具体可用参数请参考：
         * http://docs.guzzlephp.org/en/stable/request-options.html
         *
         * - retries: 重试次数，默认 1，指定当 http 请求失败时重试的次数。
         * - retry_delay: 重试延迟间隔（单位：ms），默认 500
         * - log_template: 指定 HTTP 日志模板，请参考：https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
         */
        'http' => [
            'retries' => 1,
            'retry_delay' => 500,
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
        'mini_program' => [
            'app_id' => '',//
            'secret' => '',
            // token 和 aes_key 开启消息推送后可见
            'token' => 'your-token',
            'aes_key' => 'your-aes-key'
        ],
        /**
         * OAuth 配置
         *
         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
         * callback：OAuth授权完成后的回调页地址
         */
        'oauth' => [
            'scopes' => ['snsapi_userinfo'],
            'callback' => '/v1/wechat/oauth/callback',
        ],
    ],

    'wxpay' => [
        'appId' => 'wx6601e34e3e50ed65',
        'app_secret' => 'c03a3b769d12acba5f5c7f67bb11c3e7',

        'mch_id' => '1499995432',
        'mch_secret' => '1ihWkHMN6ikEFNkjNBbkVS7NQz1hhJnS',
        'cert_path' => CONFIG_PATH . '/pay_key/wechat/apiclient_cert.pem',
        'key_path' => CONFIG_PATH . '/pay_key/wechat/apiclient_key.pem',
        'NotifyUrl' => 'http://distribution.losingbattle.site/v1/notify/wechatjs_shop',
    ]

];