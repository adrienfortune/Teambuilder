<?php

// app/config/security.php
$container->loadFromExtension('security', [
        'encoders'       => [
            'Tm\UserBundle\Entity\Utilisateur' => 'sha512',
        ],

        'role_hierarchy' => [
            'ROLE_ADMIN'       => 'ROLE_UTILISATEUR',
            'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'],
        ],

        'providers'      => [
            'main' => [
                'id' => 'fos_user.user_provider.username',
            ],
        ],

        'firewalls'      => [
            'dev'  => [
                'pattern'  => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'pattern'     => '^/',
                'anonymous'   => true,
                'provider'    => 'main',
                'form_login'  => [
                    'login_path' => 'fos_user_security_login',
                    'check_path' => 'fos_user_security_check',
                ],
                'logout'      => [
                    'path'   => 'fos_user_security_logout',
                    'target' => '/',
                ],
                'remember_me' => [
                    'key' => true,
                ],
            ],
        ],

        'access_control' => [//        array('path' => '^/admin', 'role' => 'ROLE_ADMIN'),
        ]
    ]
);