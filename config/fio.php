<?php

return [
    'fake_api' => env('FIO_FAKE_API', false),
    'fake_api_url' => env('FIO_FAKE_API_URL', 'http://platby-banktest.test/api.php'),
    'account_number' => env('FIO_BANK_ACCOUNT_NUMBER', '1234567890/1234'),
    'token' => env('FIO_BANK_TOKEN', 'fewFIEWifhěoišfhfeWOIF32423'),
    'http_handler' => env('FIO_HTTP_HANDLER', 'stream'),
    'force_http1' => filter_var(env('FIO_FORCE_HTTP1', true), FILTER_VALIDATE_BOOL),
];
