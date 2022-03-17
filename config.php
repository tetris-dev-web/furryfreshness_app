<?php
require('./vendor/autoload.php');
define('FCPATH', dirname(__FILE__).'/');

$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load(FCPATH . '.env');

$config['SHOPIFY_APP_HOST_NAME'] = 'furryfreshness.myshopify.com';
$config['SHOPIFY_API_VERSION'] = '2022-01';

Shopify\Context::initialize(
  $_ENV['SHOPIFY_API_KEY'],
  $_ENV['SHOPIFY_API_SECRET'],
  $_ENV['SHOPIFY_APP_SCOPES'],
  $config['SHOPIFY_APP_HOST_NAME'],
  new Shopify\Auth\FileSessionStorage(FCPATH . 'tmp/php_sessions'),
  $config['SHOPIFY_API_VERSION'],
  false,
  true,
  $_ENV['SHOPIFY_STOREFRONT_ACCESS_TOKEN']
);
?>