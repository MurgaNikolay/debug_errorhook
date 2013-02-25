<?php


return [
    'notifiers' => [
        [
            'class' => 'Debug\ErrorHook\SmtpMailNotifier',
            'options' => [
                'to' => ["info@example.com"],
                'from' => 'info@example.com',
                'charset' => 'utf-8',
                'subj_prefix' => '[ERROR] ', // Default: [ERROR]
                'what_to_log' => \Debug\ErrorHook\TextNotifier::LOG_ALL,
                'smtp' => [
                    'host' => 'localhost',
                    'port' => 25,
                    'user' => '',
                    'pass' => '',
                    'auth' => true,
                    'secure' => null,
                ],
            ],
            'decorators' => [
                [
                    'class' => 'Debug\ErrorHook\RemoveDupsWrapper',
                    'options' => [
                        'tmp_path' => '/tmp/errors',
                        'period' => 300,
                    ]
                ]
            ],
        ],
        /*//Using mail() function
        [
            'class' => 'Debug\ErrorHook\MailNotifier',
            'options' => [
                'to' => ["info@example.com"],
                'from' => 'info@example.com',
                'what_to_log' => \Debug\ErrorHook\TextNotifier::LOG_ALL,
            ],
            'decorators' => [
                [
                    'class' => 'Debug\ErrorHook\RemoveDupsWrapper',
                    'options' => [
                        'tmp_path' => '/tmp/errors',
                        'period' => 300,
                    ]
                ]
            ],
        ],
        */
    ],
];