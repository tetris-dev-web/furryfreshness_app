<?php
/******************************
* File Name : Master_model.php
* Description : Root model for other models
* Version : 1.0.1
* Author : Jubin Ri
* Last Update : 2018.05.17
*******************************/
class Master_model extends CI_Model {
  protected $_tablename = "";
  protected $_total_count = 0;
  protected $_arrCondition = array();
  protected $_getlistFields = '*';
  
  function __construct() {
    parent::__construct();
  }
  
  /** Check the record is exist
  * $arr = array(
  *   'table1' => 'where cluase 1' ),
  *   'table2' => 'where cluase 2' ),
  *   'table3' => 'where cluase 3' ),
  * );
  * 
  * return : if one of them is exist, return true
  */
  protected function checkExist( $whereClause )  {
    $return = false;
    
    $sql = 'SELECT id FROM ' . $this->_tablename . ' WHERE ' . $whereClause;
    $query = $this->db->query($sql);

    // if any of one is exist, return true
    if( $query->num_rows() > 0 ) {
      $return = true;
    }
    
    return $return;
  }
  
  /**
  * Add a new a record
  * 
  * @param mixed $data : a recordset
  */
  public function add( $data ) {
      $this->db->insert( $this->_tablename, $data);
      if($this->db->affected_rows() > 0) {    
          return true;
      } else {
          return false;
      }
  }

  /**
  * Delete a record
  * 
  * @param mixed $id : primary key
  */
  public function delete( $id )  {
    $this->db->where('id', $id);
    $this->db->delete( $this->_tablename, array( 'id' => $id ) );
    if( $this->db->affected_rows() > 0 )
        return true;
    else
        return false;
  }

  /**
  * Update a record
  * 
  * @param mixed $id : primary key
  * @param mixed $data : record data to be updated
  */
  public function update( $id, $data ) {
    $this->db->where('id', $id);
    $this->db->update( $this->_tablename, $data);        
    
    if( $this->db->affected_rows() > 0 )
        return true;
    else
        return false;
  }

  /**
  * Get the list with the dedicated where and order by clase
  * 
  * @param mixed $where : where clause as string
  * @param mixed $order_by : order by clause
  */
  public function getTotalCount(){ return $this->_total_count; }
  
  /**
  * Get the list of product/ varints
  * @param mixed $arrCondition : array(
  *     'sort' => '',                   // String "{column} {order}"
  *     'page_number' => '',            // Int, default : 0
  *     'page_size' => '',              // Int, default Confing['PAGE_SIZE'];
  * );
  */
  public function getList( $arrCondition ) {
    $where = array();

    // Build the where clause
    foreach ($this->_arrCondition as $field => $type) {
      // Work with selected condition
      if (!array_key_exists($field, $arrCondition)) continue;

      // According to the condition type, we change the SQL format
      switch ($type) {
        case '%LIKE%':
          $where[$field . " LIKE '%" . str_replace( "'", "\\'", $arrCondition[$field] ) . "%'"] = '';
          break;    
        case 'LIKE%':
          $where[$field . " LIKE '" . str_replace( "'", "\\'", $arrCondition[$field] ) . "%'"] = '';
          break;    
        case '%LIKE':
          $where[$field . " LIKE '%" . str_replace( "'", "\\'", $arrCondition[$field] ) . "'"] = '';
          break;    
        case '=':
          $where[$field] = $arrCondition[$field];
          break;    
        case '=%':
          if ($arrCondition[$field] != '' ) $where[$field] = $arrCondition[$field];
          break;    
        case '=%0':
          if ($arrCondition[$field] != '' && $arrCondition[$field] != '0') $where[$field] = $arrCondition[$field];
          break;    
        case 'IN':
          if ($arrCondition[$field] != '' && $arrCondition[$field] != 0) $where[$field . " IN (" . $arrCondition[$field] . ")"] = '';
          break;
        case 'BETWEEN':
          $arr = explode('TO', $arrCondition[$field]);
          $from = $arrCondition[$field];
          $to = $arrCondition[$field];
          if (count($arr) == 2) {
            $from = $arr[0];
            $to = $arr[1];
          }
          $where[$field . " >= '" . $from . "' AND " . $field . " <= '" . $to . "'"] = '';
          break;    
      }
    }

    // Get the count of records
    foreach( $where as $key => $val )
    if( $val == '' )
        $this->db->where( $key );
    else
        $this->db->where( $key, $val );
    $query = $this->db->get( $this->_tablename);
    $this->_total_count = $query->num_rows();
    
    // Select fields
    $select = !empty( $arrCondition['is_all'] ) ? '*' : $this->_getlistFields ;
    $this->db->select( $select );
    
    // Sort
    if( isset( $arrCondition['sort'] ) ) {
      $this->db->order_by( $arrCondition['sort'] );
    } else {
      $this->db->order_by( 'id', 'DESC' );
    }

    // Limit
    if( isset( $arrCondition['page_number'] )) {
        $page_size = isset( $arrCondition['page_size'] ) ? $arrCondition['page_size'] : $this->config->item('PAGE_SIZE');
        $this->db->limit( $page_size, $arrCondition['page_number'] );
    }

    foreach( $where as $key => $val )
    if( $val == '' )
        $this->db->where( $key );
    else
        $this->db->where( $key, $val );
    $query = $this->db->get( $this->_tablename );

    return $query;
  }
      
  /**
  * get the one record for relevant id
  *     
  * @param mixed $id
  */
  public function getInfo( $id ) {
    $this->db->where( 'id', $id );
    $query = $this->db->get( $this->_tablename );
    $result = $query->result();
    
    return $result[0];
  }
  
  /**
  * Drop table
  *     
  * @param mixed $id
  */  
  function uninstall() {
    $this->db->delete( $this->_tablename );
  }
  
  /**
  * Truncate table
  *     
  * @param mixed $id
  */
  function clear() {
    $this->db->truncate($this->_tablename);
  }  
}  
?>