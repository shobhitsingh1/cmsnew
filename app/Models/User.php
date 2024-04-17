<?php


namespace App\Models;

use CodeIgniter\Model;

class User extends Model {

  

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	

	function getUserName($id){
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_admin_user');
        $builder->where('id', $id);
		$query_admin_users =  $builder->get();
        $query_admin_users =  $query_admin_users->getResultObject();

		if(count($query_admin_users) > 0){
            foreach ($query_admin_users as $row) {
                return $row->user_name;
            }
		
		}else{
		
			return "N/A";
		
		}
		
	
	}
    
   
}