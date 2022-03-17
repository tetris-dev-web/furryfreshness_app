<?php
/******************************
* File Name : Accounts.php
* Description : Account Management API Controller
* Version : 0.9.0
* Author : Jubin Ri
* Last Update : 2022.02.02
*******************************/

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {
	// Error messages
	private $_arrErrorCode = array (
		'E001' =>	'Action Required',
		'E002' =>	'Account Holder is missing',
		'E003' =>	'Account Number is missing',
		'E004' =>	'Phone Number is missing',
		'E005' =>	'Invalid Account Number Format, it should only contain number, space only.',
		'E006' =>	'Invalid Phone Number Format',
		'E007' =>	'Given Account Number is already exist',
		'E008' =>	'id is missing',
		'E009' =>	'id should be numeric format',
		'E010' =>	'Can\'t find account for given id',
		'E100' =>	'Unknown Error'
	);

	public function __construct() {
    parent::__construct();
    $this->load->model('Account_model');

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

	/**
	 * Get Account List
	 */
	private function _getAccounts($arrInput) {
		// Build search condition
		$searchParam = array (
			'account_holder'	=> isset($arrInput['account_holder']) ? $arrInput['account_holder'] : '', 
			'id'	=> isset($arrInput['id']) ? $arrInput['id'] : '', 
		);
		if (isset($arrInput['account_number'])) $searchParam['account_number'] = $arrInput['account_number'];
		if (isset($arrInput['phone_number'])) $searchParam['phone_number'] = $arrInput['phone_number'];

		// Fetch data
		$query = $this->Account_model->getList($searchParam);

		// Build Return data
		$arrReturn = array ();
		foreach ($query->result() as $row) $arrReturn[] = array (
			'id'							=> $row->id,
			'account_holder'	=> $row->account_holder,
			'account_number'	=> $row->account_number,
			'phone_number'		=> $row->phone_number,
			'notes'						=> $row->notes,
		);

		$this->_print_json($arrReturn);
	}

	/**
	 * Add a new account
	 */
	private function _addAccount($arrInput) {
		// Input data validation
		if (!isset($arrInput['account_holder'])) $this->_print_json_error('E002');
		if (!isset($arrInput['account_number'])) $this->_print_json_error('E003');
		if(preg_match("/^[ 0-9]+$/", $arrInput['account_number']) != TRUE)  $this->_print_json_error('E005');
		if (!isset($arrInput['phone_number'])) $this->_print_json_error('E004');
		if(preg_match("/^[0-9+ \+\(\)-]+$/", $arrInput['phone_number']) != TRUE)  $this->_print_json_error('E006');

		// Build Input data
		$arrData = array (
			'account_holder' => trim($arrInput['account_holder']),
			'account_number' => trim($arrInput['account_number']),
			'phone_number' => trim($arrInput['phone_number']),
			'notes' => isset($arrInput['notes']) ? trim($arrInput['notes']) : '',
		);

		// Check account is exists already
		if ($this->Account_model->checkAccountExist($arrData['account_number'])) $this->_print_json_error('E007');

		// Add data
		if(!$this->Account_model->add($arrData)) $this->_print_json_error('E100');

		$this->_print_json(array());
	}

	/**
	 * Update an existing account
	 */
	private function _updateAccount($arrInput) {
		// Input data validation
		if (!isset($arrInput['id'])) $this->_print_json_error('E008');
		if (preg_match("/^[0-9]+$/", $arrInput['id']) != TRUE)  $this->_print_json_error('E009');
		if (!isset($arrInput['account_holder'])) $this->_print_json_error('E002');
		if (!isset($arrInput['account_number'])) $this->_print_json_error('E003');
		if (preg_match("/^[ 0-9]+$/", $arrInput['account_number']) != TRUE)  $this->_print_json_error('E005');
		if (!isset($arrInput['phone_number'])) $this->_print_json_error('E004');
		if(preg_match("/^[0-9+ \+\(\)-]+$/", $arrInput['phone_number']) != TRUE)  $this->_print_json_error('E006');

		// Build Input data
		$arrData = array (
			'account_holder' => trim($arrInput['account_holder']),
			'account_number' => trim($arrInput['account_number']),
			'phone_number' => trim($arrInput['phone_number']),
			'notes' => isset($arrInput['notes']) ? trim($arrInput['notes']) : '',
		);

		// Check account is exists already
		if ($this->Account_model->checkAccountExist($arrData['account_number'], trim($arrInput['id']))) $this->_print_json_error('E007');

		// Update data
		if(!$this->Account_model->update(trim($arrInput['id']), $arrData)) $this->_print_json_error('E010');

		$this->_print_json(array());
	}

	/**
	 * Delete an existing account
	 */
	private function _deleteAccount($arrInput) {
		// Input data validation
		if (!isset($arrInput['id'])) $this->_print_json_error('E008');
		if (preg_match("/^[0-9]+$/", $arrInput['id']) != TRUE)  $this->_print_json_error('E009');

		// Delete data
		if(!$this->Account_model->delete(trim($arrInput['id']))) $this->_print_json_error('E010');

		$this->_print_json(array());
	}
}