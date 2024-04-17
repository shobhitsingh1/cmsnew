<?php

namespace App\Controllers;

use Config\Database;

use App\Models\Tags;
use App\Models\User;
use CodeIgniter\Router\Router;

class Users extends BaseController
{


    public function index()
    {
        $session = \Config\Services::session();
        $user_data = $session->get();
        $db = \Config\Database::connect();

        $query = $db->table('tbl_admin_user')
            ->select('*')
            ->get();

        $query_admin_users =  $query->getResultObject();

        $session = \Config\Services::session();
        $sessionData = $session->get();
        $router = service('router');
        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);

        $data = [
            'user_data' => $sessionData,
            'class_name' =>  $class,
            'query_admin_users' => $query_admin_users
        ];

        $content = view('content/users', $data);
        $title = [
            'title' => 'CMS: Users',
            'content' => $content
        ];
        return view('template', $title);

    }

    function create_users()
    {


        $cssFiles = [
            'public/assests/css/jquery.multiselect_new.css',
            'public/assests/css/jquery.multiselect.filter.css',
            'public/assests/css/colorbox.css',
            'public/assests/css/Selectyze.jquery.css',
        ];


        $jsFiles = [
            'public/assests/js/jquery.multiselect_new.js',
            'public/assests/js/jquery.multiselect.filter.js',
            'public/assests/js/jquery.colorbox.js',
            'public/assests/js/jquery.lavalamp.min.js',
            'public/assests/js/Selectyze.jquery.js',
            'public/assests/js/jquery.validate.js',
        ];

        $query_admin_users = [];
        $db = \Config\Database::connect();

        if (isset($_GET['id'])) {

            $user_id = $this->request->getGet('id');

            $query = $db->table('tbl_admin_user')
                ->select('*')
                ->where('id', $user_id)
                ->get();

            $query_admin_users =  $query->getResultObject();

        }
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            if ($action == 'delete') {
                $user_id = $this->request->getGet('id');

                 $db->table('tbl_admin_user')
                    ->where('id', $user_id)
                    ->delete();

                return redirect()->to(base_url('users.php'));

            }

        }
        if (isset($_POST['cmdSaveAdminUser'])) {

            $admin_user = (isset($_POST['user_privileges'])) ? '1' : '0';
            $insertArray = array(
                "user_name" => ($_POST['user_name']),
                "user_email" => ($_POST['user_email']),
                "user_password" => md5($_POST['user_password']),
                "user_status" => '1',
                "super_admin" => $admin_user

            );

            $query = $db->table('tbl_admin_user')
                ->select('*')
                ->where('user_email', $_POST['user_email'])
                ->get();

            $query = $query->getResultObject();

            if(count($query) == 0){
                $db->table('tbl_admin_user')->insert($insertArray);
                return redirect()->to(base_url('users.php'));
            }


        }
        if (isset($_POST['cmdUpdateAdminUser'])) {
            $admin_user = (isset($_POST['user_privileges'])) ? '1' : '0';
            if (empty($_POST['user_password'])) {
                $insertArray = array(
                    "user_name" => ($this->request->getPost('user_name')),
                    "user_email" => ($this->request->getPost('user_email')),
                    "super_admin" => $admin_user

                );
            } else {
                $insertArray = array(
                    "user_name" => ($this->request->getPost('user_name')),
                    "user_email" => ($this->request->getPost('user_email')),
                    "super_admin" => $admin_user,
                    "user_password" => md5($_POST['user_password'])

                );

            }
            if (!empty($query)) {
                $db->table('tbl_admin_user')
                    ->where('id',$this->request->getPost('user_id'))
                    ->update($insertArray);
            }


            return redirect()->to(base_url('users.php'));


        }

        $session = \Config\Services::session();
        $sessionData = $session->get();
        $router = service('router');

        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);
        $data = [
            'cssFiles' => $cssFiles,
            'jsFiles' => $jsFiles,
            'session_data' => $sessionData,
            'class_name' => $class,
            'admin_data' => $query_admin_users
        ];

        $content = view('content/create_user', $data);
        $title = [
            'title' => 'CMS: Create User',
            'content' => $content,
            'user_data' => $sessionData
        ];
        return view('template', $title);

    }

    function checkuser()
    {

        $db = \Config\Database::connect();


        $query = $db->table('tbl_admin_user')
            ->select('*')
            ->where('user_name',  $_GET['user_name'])
            ->get();
        $query_admin_users = $query->getResultObject();

        if (count($query_admin_users) == 0) {
            echo "true";
        } else {
            echo "false";
        }
    }
}
