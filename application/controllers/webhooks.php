<?php
/******************************
* File Name : Webhooks.php
* Description : Webhook Management API Controller
* Version : 0.9.0
* Author : Jubin Ri
* Last Update : 2022.03.17
*******************************/

defined('BASEPATH') OR exit('No direct script access allowed');
use Shopify\Clients\Rest;

class Webhooks extends CI_Controller {

	public function __construct() {
    parent::__construct();

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

    header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  }

	/**
	 * Render JSON string with formatt
	 */
	private function _print_json ($arrData, $errorCode = '' ) {
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
	private function _print_json_error($errorCode) {
		$this->_print_json(array(), $errorCode);
	}

	/**
	 * Endpoint for accounts
	 */
	public function index() {
		switch($_SERVER['REQUEST_METHOD']){
			case 'GET':
				$var_array=$this->input->get();
				$this->_getAccounts($var_array);
				break;
			case 'POST':
				$var_array = json_decode(file_get_contents("php://input"), true);
				$this->_addAccount($var_array);
				break;
			case 'PUT':
				$var_array = json_decode(file_get_contents("php://input"), true);
				$this->_updateAccount($var_array);
				break;
			case 'DELETE':
				$var_array = json_decode(file_get_contents("php://input"), true);
				$this->_deleteAccount($var_array);
				break;
			default:
				$this->_print_json(array(), 'E001');
		}
	}

  public function checkouts_create() {
    $var_array = json_decode(file_get_contents("php://input"), true);

    print_r($var_array);
  }

  public function register() {

    $client = new Rest($this->config->item('SHOPIFY_APP_HOST_NAME'), '');
    $response = $client->post(
      "webhooks",
      [
        "webhook" => [
          "topic" => "checkouts/create",
          "address" => $_ENV['SHOPIFY_APP_URL'] . 'webhooks/checkouts_create',
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

  public function list() {
    $client = new Rest($this->config->item('SHOPIFY_APP_HOST_NAME'), '');
    $response = $client->get(
      "webhooks"
    );

    echo '<PRE>';
    if ($response->getStatusCode() == '200')
      print_r($response->getDecodedBody());
    else
    print_r($response);
  }

  public function delete($id) {
    $client = new Rest($this->config->item('SHOPIFY_APP_HOST_NAME'), '');
    $response = $client->delete(
      "webhooks/" . $id
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
	public function graphql() {

    $storefrontClient = new Shopify\Clients\Storefront($this->config->item('SHOPIFY_APP_HOST_NAME'), '');

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
}