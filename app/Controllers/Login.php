<?php

namespace App\Controllers;

class Login extends BaseController {


    public function index()
    {

        $data = array();
//        if(ISSET($_POST['cmdLogin'])){
//            echo "dsa";
//            $userName = $this->request->post('userid');
//            $usrpwd = $this->request->post('usrpwd');
//
//            $db = \Config\Database::connect();
//
//            $query = $db->table('tbl_admin_user')
//                ->select('*')
//                ->where('user_status', '1')
//                ->where('user_name', $userName)
//                ->where('user_password', md5($usrpwd))
//                ->get();
//
//            $query_admin_users = $query->getResult();
//
//            print_r($query_admin_users);exit;
//            if($query_admin_users->num_rows() > 0){
//                $row = $query_admin_users->row();
//
//                $super_admin = ($row->super_admin == '1')?"super_admin":'';
//
//                $this->db->select('*');
//                $this->db->where('user_id', $row->id);
//
//                $this->db->from('tbl_color_preference');
//
//                $users_colors = $this->db->get();
//                if($users_colors->num_rows() > 0){
//                    $colors = (array)$users_colors->row();
//                    $sessiondata = array(
//                        'username'  => $row->user_name,
//                        'username_id'     => $row->id,
//                        'logged_in' => TRUE,
//                        'super_admin' => $super_admin,
//                        'header_text_color' => $colors['header_text_color'],
//                        'header_bg_color'=> $colors['header_bg_color'],
//                        'field_heading_text_color' => $colors['field_heading_text_color'],
//                        'field_txt_color' => $colors['field_txt_color'],
//                        'devotional_text_color' => $colors['devotional_text_color'],
//                        'page_bg_color' => $colors['page_bg_color'],
//                        'devotional_bg_color' => $colors['devotional_bg_color'],
//                        'button_text_color' => $colors['button_text_color'],
//                        'button_bg_color' => $colors['button_bg_color'],
//                        'export_bg_color' => $colors['export_bg_color'],
//                        'export_text_color' => $colors['export_text_color'],
//                        'user_color' => $colors['user_color'],
//                        'fonts' => $colors['fonts']
//                    );
//
//                }else{
//                    $sessiondata = array(
//                        'username'  => $row->user_name,
//                        'username_id'     => $row->id,
//                        'logged_in' => TRUE,
//                        'super_admin' => $super_admin,
//                        'header_text_color' => '',
//                        'header_bg_color'=> '',
//                        'field_heading_text_color' => '',
//                        'field_txt_color' => '',
//                        'devotional_text_color' => '',
//                        'page_bg_color' => '',
//                        'devotional_bg_color' => '',
//                        'user_color' => '',
//                        'button_text_color' => '',
//                        'button_bg_color' => '',
//                        'export_bg_color' => '',
//                        'export_text_color' => '',
//                        'fonts' => ''
//
//                    );
//
//
//                }
//
//                $this->session->set_userdata($sessiondata);
//                redirect('library');
//            }else{
//                $data['error'] = 'Invalid Username and Password';
//
//            }
//
//
//
//        }
        return view('content/login_new',$data);
    }

    public function checkLogin(){
        $user_id = $this->request->getPost('userid');
        if($user_id != ""){
            $userName = $this->request->getPost('userid');
            $usrpwd = $this->request->getPost('usrpwd');

            $db = \Config\Database::connect();

            $query = $db->table('tbl_admin_user')
                ->select('*')
                ->where('user_status', '1')
                ->where('user_name', $userName)
                ->where('user_password', md5($usrpwd))
                ->get();

            $query_admin_users = $query->getResultObject();


            if (count($query_admin_users) > 0 ) {
                $row = $query_admin_users[0];

                $super_admin = ($row->super_admin == '1') ? "super_admin" : '';

                $users_colors = $db->table('tbl_color_preference')
                    ->select('*')
                    ->where('user_id', $row->id)
                    ->get();

                if ($users_colors->getNumRows() > 0) {
                    $colors = (array)$users_colors->getRow();
                    $sessiondata = [
                        'username'                  => $row->user_name,
                        'username_id'               => $row->id,
                        'logged_in'                 => TRUE,
                        'super_admin'               => $super_admin,
                        'header_text_color'         => $colors['header_text_color'],
                        'header_bg_color'           => $colors['header_bg_color'],
                        'field_heading_text_color'  => $colors['field_heading_text_color'],
                        'field_txt_color'           => $colors['field_txt_color'],
                        'devotional_text_color'     => $colors['devotional_text_color'],
                        'page_bg_color'             => $colors['page_bg_color'],
                        'devotional_bg_color'       => $colors['devotional_bg_color'],
                        'button_text_color'         => $colors['button_text_color'],
                        'button_bg_color'           => $colors['button_bg_color'],
                        'export_bg_color'           => $colors['export_bg_color'],
                        'export_text_color'         => $colors['export_text_color'],
                        'user_color'                => $colors['user_color'],
                        'fonts'                     => $colors['fonts']
                    ];
                } else {
                    $sessiondata = [
                        'username'                  => $row->user_name,
                        'username_id'               => $row->id,
                        'logged_in'                 => TRUE,
                        'super_admin'               => $super_admin,
                        'header_text_color'         => '',
                        'header_bg_color'           => '',
                        'field_heading_text_color'  => '',
                        'field_txt_color'           => '',
                        'devotional_text_color'     => '',
                        'page_bg_color'             => '',
                        'devotional_bg_color'       => '',
                        'user_color'                => '',
                        'button_text_color'         => '',
                        'button_bg_color'           => '',
                        'export_bg_color'           => '',
                        'export_text_color'         => '',
                        'fonts'                     => ''
                    ];
                }

                $session = \Config\Services::session();
                $session->set($sessiondata);
                return redirect()->to(base_url('library.php'));
            } else {
                $router = service('router');
                $class = $router->controllerName();
                $className = substr($class, strrpos($class, '\\') + 1);
                $class = strtolower($className);
                $admin_data['super_admin'] = '';
                $admin_data['username'] = '';
                $data = [
                    'cssFiles' => '',
                    'jsFiles' => '',
                    'user_data' => $admin_data,
                    'class_name' =>  $class,
                    'query_admin_users' => $query_admin_users
                ];

                $data['error'] = 'Invalid Username and Password';
                return view('content/login_new', $data);

            }

        }
}
function logout(){

        $sessiondata = array(
            'username'  => '',
            'username_id'     => '',
            'logged_in' => false,
            'super_admin' => ''

        );
    // Get session service instance
    $session = \Config\Services::session();
    $session->destroy();
    return redirect()->to(base_url('/'));
    }
}