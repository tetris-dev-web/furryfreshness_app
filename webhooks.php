<?php
require('./config.php');
use Shopify\Clients\Rest;

/**
 * Render JSON string with formatt
 */
function _print_json ($arrData, $errorCode = '' ) {
  $return = array(
    'result' => $errorCode,
    'message' => $errorCode == '' ? '' : $this->_arrErrorCode[$errorCode],
    'data' => $arrData
  );

  header('Content-type: application/json');
  echo json_encode($return);
  exit;
}

/**
 * Render Error json string only
 */
function _print_json_error($errorCode) {
  $_print_json(array(), $errorCode);
}

function checkouts_create() {
  $var_array = json_decode(file_get_contents("php://input"), true);

  print_r($var_array);
}

function register() {

  $client = new Rest($config['SHOPIFY_APP_HOST_NAME'], '');
  $response = $client->post(
    "webhooks",
    [
      "webhook" => [
        "topic" => "checkouts/create",
        "address" => $_ENV['SHOPIFY_APP_URL'] . 'webhooks.php?action=checkouts_create',
        "format" => "json"
      ]
    ]
  );

  echo '<PRE>';
  if ($response->getStatusCode() == '201')
    print_r($response->getDecodedBody());
  else
  print_r($response);
}

function showlist() {
  $client = new Rest($config['SHOPIFY_APP_HOST_NAME'], '');
  $response = $client->get(
    "webhooks"
  );

  echo '<PRE>';
  if ($response->getStatusCode() == '200')
    print_r($response->getDecodedBody());
  else
  print_r($response);
}

function delete() {
  $client = new Rest($config['SHOPIFY_APP_HOST_NAME'], '');
  $response = $client->delete(
    "webhooks/" . $_GET['id']
  );

  echo '<PRE>';
  if ($response->getStatusCode() == '200')
    print_r($response->getDecodedBody());
  else
  print_r($response);
}

/**
 * Get Account List
 */
function graphql() {

  $storefrontClient = new Shopify\Clients\Storefront($config['SHOPIFY_APP_HOST_NAME'], '');

  $products = $storefrontClient->query(
    <<<QUERY
    {
      products (first: 10) {
        edges {
          node {
            id
            title
            variants (first: 10 ) {
              edges {
                node {
                  id
                  title
                }
              }
            }
          }
        }
      }
    }
    QUERY,
  );

  echo '<PRE>';
  print_r($products->getDecodedBody());
}

$action = $_GET['action'];

switch ($action) {
  case 'checkouts_create': checkouts_create(); break;
  case 'register': register(); break;
  case 'showlist' : showlist(); break;
  case 'delete' : delete(); break;
  case 'graphql' : graphql(); break;
  default: break;
}
?>