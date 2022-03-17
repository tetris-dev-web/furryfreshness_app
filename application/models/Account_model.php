<?php
/******************************
* File Name : Account_model.php
* Description : Account Management Model
* Version : 1.0.0
* Author : Jubin Ri
* Last Update : 2022.02.02
*******************************/

class Account_model extends Master_model {
  protected $_tablename = 'account';
  protected $_arrCondition = array(
    'id' => '=%',
    'account_holder' => '%LIKE%',
    'account_number' => '=',
    'phone_number' => '=',
    'notes' => '%LIKE%'
  );

  function __construct() {
    parent::__construct();
  }

  /**
   * Check if account number exist already
   * 
   * @param mixed $accountNumber : account number to be checked for exist
   * @param mixed $skipId : if skipId isn't blank, we don't check that record for account number
   */
  public function checkAccountExist($accountNumber, $skipId = '') {
    $strWhereClause = 'account_number = \'' . $accountNumber . '\'';
    if ($skipId != '') $strWhereClause .= ' AND id <> ' . $skipId;
    return $this->checkExist($strWhereClause);
  }
}  
?>