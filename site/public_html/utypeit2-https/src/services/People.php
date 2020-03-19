<?php

require_once(SERVICES.'BaseService.php');

class People extends BaseService
{
	
    /*
    *
    * Gets a person by their id
    *
    */
    public function getPerson($id) {
        return($this->sendAndGetOne('SELECT * FROM People WHERE id="'.$id.'"'));
    }
	/*
	*
	* Adds a new customer and (if necessary) creates the organization record
	*
	*/
	public function addPerson($person) {
		
		$keystr = '';
		$valstr = '';
		foreach($person AS $k=>$v) {
			$keystr .= $k.',';
			$valstr .= "'".$v."',";
		}
		$keystr = substr($keystr, 0, -1);
		$valstr = substr($valstr, 0, -1);
		$query = "INSERT INTO People (".$keystr.") VALUES (".$valstr.")";
		return( $this->insertAndGetOne($query) );
	}
	
	/*
	*
	* Adds a new customer and (if necessary) creates the organization record
	*
	*/
	public function updatePerson($person) {
        
		$person_id = $person['id'];
		unset($person['id']);
		$updatestr = '';
		$query = "UPDATE People SET ";
		foreach($person AS $k=>$v) {
			if($v) {
				$updatestr .= $k."='".$v."',";
			} else {
				$updatestr .= $k."='0',";
			}
		}
		$updatestr = substr($updatestr,0,-1);
		$query .= $updatestr." WHERE id='".$person_id."'";
		$result = $this->sendAndGetOne($query);
		return($person_id);

	}
	
	/*
	*
	* Retrieves a short list of all customers
	*
	*/
	public function getPeopleList($type,$start='',$limit='',$orderby='') {
		if($orderby=='') {
			$orderby = 'id';
		}

		if($type == 'customers'){
			$query = "SELECT DISTINCT(People.id),CONCAT(People.first_name,' ',People.last_name) AS last_name,People.email,Order_People.level AS order_level,People.status FROM People,Order_People WHERE People.level='1' AND Order_People.person_id=People.id ORDER BY ".$orderby." ASC";
		} else if($type == 'users') {
			$query = "SELECT id,CONCAT(first_name,' ',last_name) AS last_name,email,level,status FROM People WHERE level>'6' AND type='2' ORDER BY $orderby";
		} else if($type == 'contractors') {
			$query = "SELECT id,CONCAT(first_name,' ',last_name) AS last_name,email,state,status FROM People WHERE level=6 AND type='2' ORDER BY $orderby";
		}

		if($limit) {
			$query .= " LIMIT ".$start.",".$limit;
		}
        
		$peoplelist = $this->sendAndGetMany($query);
		//print_r($peoplelist);

		return($peoplelist);
		
	}
	
	/*public function getPeopleByLevel($level) {
		$query = "SELECT id,first_name,last_name FROM People WHERE level='".$level."' && status='1'";
		return( $this->sendAndGetMany($query) );
	}*/
    
    public function getOrderPerson($id,$order_id) {
        $query = "SELECT People.*,Order_People.level AS order_level,Order_People.status AS user_status FROM People,Order_People WHERE Order_People.person_id='".$id."' AND Order_People.order_id='".$order_id."' AND People.id=Order_People.person_id";
        $res = $this->sendAndGetOne($query);
        return($res);
    }
	
	public function getPeopleCount($type='') {
		$query = "SELECT COUNT(id) AS COUNT FROM People";
		switch($type) {
			case 'customers':
				$query .= " WHERE level<'6'";
				break;
			case 'users':
				$query .= " WHERE level>'6'";
				break;
			case 'contractors':
				$query .= " WHERE level='6'";
				break;
		}
		$res = $this->sendAndGetOne($query);
		return($res->COUNT);
	}
	
	public function getPersonByUsernamePassword($username,$password) {
		$query = "SELECT * FROM People WHERE login='$username' AND password='$password'";
		$res = $this->sendAndGetOne($query);
		if(!$res) {
			$query = "SELECT * FROM People WHERE login='".urlencode($username)."' AND password='".urlencode($password)."'";
			$res = $this->sendAndGetOne($query);
		}
		return( $res );
	}
	
	public function getOrderPeople($order_id,$start=null,$limit=null,$orderby=null) {
		
		// Get the people associated with this order
		
		$query = "SELECT People.*,Order_People.level AS order_level, Order_People.status AS user_status FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND People.id=Order_People.person_id";
		if($orderby) {
		    if($orderby != 'level') {
    		    $query .= " ORDER BY People.".$orderby." ASC";
            } else {
                $query .= " ORDER BY Order_People.".$orderby." ASC";
            }
		}
		if($limit) {
		    $query .= " LIMIT ".$start.",".$limit;
		}
        //echo $query;
		$res = $this->sendAndGetMany($query);
				
		return($res);
		
	}
	
	public function removePerson($person_id) {
		$this->sendAndDelete("DELETE FROM People WHERE id='".$person_id."'");
		$this->sendAndDelete("DELETE FROM Order_People WHERE person_id='".$person_id."'");
		$this->sendAndDelete("DELETE FROM Orders_Contractors WHERE contractor_id='".$person_id."'");
	}
	
}

//$newPeople = new People();

//print_r( $newPeople->getPeopleList('contractors') );


?>