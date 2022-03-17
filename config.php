<?php
require('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load(FCPATH . '.env');

$config['SHOPIFY_APP_HOST_NAME'] = 'furryfreshness.myshopify.com';
$config['SHOPIFY_API_VERSION'] = '2021-04';

Shopify\Context::initialize(
  $_ENV['SHOPIFY_API_KEY'],
  $_ENV['SHOPIFY_API_SECRET'],
  $_ENV['SHOPIFY_APP_SCOPES'],
  $this->config->item('SHOPIFY_APP_HOST_NAME'),
  new Shopify\Auth\FileSessionStorage(FCPATH . 'tmp/php_sessions'),
  $this->config->item('SHOPIFY_API_VERSION'),
  false,
  true,
  $_ENV['SHOPIFY_STOREFRONT_ACCESS_TOKEN']
);
?>