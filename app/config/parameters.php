<?php

$container->setParameter('database_host', getenv('MYSQL_HOST'));
$container->setParameter('database_name', getenv('MYSQL_DATABASE'));
$container->setParameter('database_user', getenv('MYSQL_USER'));
$container->setParameter('database_password', getenv('MYSQL_PASSWD'));
$container->setParameter('secret', getenv('SECRET_KEY'));
$container->loadFromExtension('doctrine', array('dbal' => array(
    'driver'   => 'pdo_mysql',
    'dbname'   => '%database_name%',
    'user'     => '%database_user%',
    'password' => '%database_password%',
)));
