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

  error_log("Webhook Catched - checkouts_create");
  error_log(json_encode($var_array));
}

function carts_update() {
  $var_array = json_decode(file_get_contents("php://input"), true);

  error_log("Webhook Catched - cart_update");
  error_log(json_encode($var_array));
}

function register_checkout() {
  global $config;

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

function register_cart() {
  global $config;

  $client = new Rest($config['SHOPIFY_APP_HOST_NAME'], '');
  $response = $client->post(
    "webhooks",
    [
      "webhook" => [
        "topic" => "carts/update",
        "address" => $_ENV['SHOPIFY_APP_URL'] . 'webhooks.php?action=carts_update',
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
  global $config;

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
  global $config;

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
  global $config;

  $storefrontClient = new Shopify\Clients\Storefront($config['SHOPIFY_APP_HOST_NAME'], '');

  if (false) {
    $checkoutId = "Z2lkOi8vc2hvcGlmeS9DaGVja291dC82OTNlODVlYmNlNWI2MmY5NDBlMmJmNDVmODJkYjBhOT9rZXk9ZGZhMzVlNjk5OTJhMTJhYWE4NGU4MDA5NTQ2ODI1MDk="; // Storefront API
    $checkoutId = "Z2lkOi8vc2hvcGlmeS9DaGVja291dC9kNDkxZDY2OWNkZWU0ZDAwYmFkYjJiNTRiMDIwNjQwMz9rZXk9ODk0ZmNiZWE3MDIwZGFlNDU1MDIzMGNkYjNmZmI4OTI="; // Web Checkout
    $queryString = <<<QUERY
    mutation {
      checkoutLineItemsReplace(lineItems: [{ variantId: "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zOTc2NDU1NDQ4MTgwMw==", quantity: 3 }], checkoutId: "{$checkoutId}",
      ) {
        checkout {
           id
           webUrl
           lineItems(first:2) {
             edges {
               node {
                 id
                 title
                 quantity
               }
             }
           }
        }
      }
    }
    QUERY;
  
    $queryString = <<<QUERY
    query {
      node(id:"{$checkoutId}" ) {
        ... on Checkout {
          id
          webUrl
        }
      }
    }
    QUERY;
  }

  $queryString = <<<QUERY
  mutation {
    cartCreate(
      input: {
        lines: [
          {
            quantity: 1
            merchandiseId: "gid://shopify/ProductVariant/39764554481803"
          }
        ]
        attributes: { key: "cart_attribute", value: "This is a cart attribute" }
      }
    ) {
      cart {
        id
        createdAt
        updatedAt
        lines(first: 10) {
          edges {
            node {
              id
              merchandise {
                ... on ProductVariant {
                  id
                }
              }
            }
          }
        }
        attributes {
          key
          value
        }
        estimatedCost {
          totalAmount {
            amount
            currencyCode
          }
          subtotalAmount {
            amount
            currencyCode
          }
          totalTaxAmount {
            amount
            currencyCode
          }
          totalDutyAmount {
            amount
            currencyCode
          }
        }
      }
    }
  }
  QUERY;

  $products = $storefrontClient->query(
    $queryString,
  );

  echo '<PRE>';
  print_r($products->getDecodedBody());
}

$action = $_GET['action'];

switch ($action) {
  case 'checkouts_create': checkouts_create(); break;
  case 'carts_update': carts_update(); break;
  case 'register_checkout': register_checkout(); break;
  case 'register_cart': register_cart(); break;
  case 'showlist' : showlist(); break;
  case 'delete' : delete(); break;
  case 'graphql' : graphql(); break;
  default: break;
}
?>