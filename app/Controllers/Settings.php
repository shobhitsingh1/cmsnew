<?php

namespace App\Controllers;

use Config\Database;

use App\Models\Tags;
use App\Models\User;
use CodeIgniter\Router\Router;

class Settings extends BaseController
{


    public function index()
    {

        $session = \Config\Services::session();
        $user_data = $session->get();
        $db = \Config\Database::connect();


        $query = $db->table('tbl_color_preference')
            ->select('*')
            ->where('user_id', $user_data['username_id'])
            ->get();

        $query_admin_users =  $query->getResultObject();



        $cssFiles = [
            'public/assests/css/jquery.multiselect_new.css',
            'public/assests/css/jquery.multiselect.filter.css',
            'public/assests/css/colorbox.css',
            'public/assests/css/Selectyze.jquery.css',
            'public/assests/css/jquery.minicolors.css',
        ];

        $jsFiles = [
            'public/assests/js/jquery.multiselect_new.js',
            'public/assests/js/jquery.multiselect.filter.js',
            'public/assests/js/jquery.colorbox.js',
            'public/assests/js/jquery.lavalamp.min.js',
            'public/assests/js/Selectyze.jquery.js',
            'public/assests/js/jquery.minicolors.js'
        ];

        if (isset($_POST['btnsubmit'])) {
            if (count($query_admin_users) == 0) {
                $insert_array = array(
                    "header_text_color" => $this->request->getPost('header_text_color'),
                    "header_bg_color" =>  $this->request->getPost('header_bg_color'),
                    "field_heading_text_color" =>  $this->request->getPost('field_heading_text_color'),
                    "field_txt_color" =>  $this->request->getPost('field_txt_color'),
                    "page_bg_color" =>  $this->request->getPost('page_bg_color'),
                    "devotional_bg_color" =>  $this->request->getPost('devotional_bg_color'),
                    "devotional_text_color" =>  $this->request->getPost('devotional_text_color'),
                    "user_color" =>  $this->request->getPost('user_color'),
                    'export_bg_color' =>  $this->request->getPost('export_bg_color'),
                    'export_text_color' =>  $this->request->getPost('export_text_color'),
                    'fonts' =>  $this->request->getPost('fonts'),
                    "user_id" => $user_data['username_id']
                );

                $query = $db->table('tbl_color_preference')
                    ->select('*')
                    ->where('user_id', $user_data['username_id'])
                    ->get();

                $query = $query->getResultObject();

                if(count($query) == 0){
                    $db->table('tbl_admin_user')->insert($insert_array);
                }

            } else {

                $insert_array = array(
                    "header_text_color" =>  $this->request->getPost('header_text_color'),
                    "header_bg_color" =>  $this->request->getPost('header_bg_color'),
                    "field_heading_text_color" =>  $this->request->getPost('field_heading_text_color'),
                    "page_bg_color" =>  $this->request->getPost('page_bg_color'),
                    "field_txt_color" =>  $this->request->getPost('field_txt_color'),
                    "devotional_bg_color" =>  $this->request->getPost('devotional_bg_color'),
                    "devotional_text_color" =>  $this->request->getPost('devotional_text_color'),
                    "user_color" =>  $this->request->getPost('user_color'),
                    'button_bg_color' =>  $this->request->getPost('button_bg_color'),
                    'button_text_color' =>  $this->request->getPost('button_text_color'),
                    'export_bg_color' =>  $this->request->getPost('export_bg_color'),
                    'export_text_color' =>  $this->request->getPost('export_text_color'),
                    'fonts' =>  $this->request->getPost('fonts')
                );


                if (!empty($query)) {
                    $db->table('tbl_color_preference')
                        ->where('user_id', $user_data['username_id'])
                        ->update($insert_array);
                }

            }

            $sessiondata = array(
                'header_text_color' =>  $this->request->getPost('header_text_color'),
                'header_bg_color' =>  $this->request->getPost('header_bg_color'),
                'field_heading_text_color' =>  $this->request->getPost('field_heading_text_color'),
                'field_txt_color' =>  $this->request->getPost('field_txt_color'),
                'devotional_text_color' =>  $this->request->getPost('devotional_text_color'),
                'page_bg_color' =>  $this->request->getPost('page_bg_color'),
                'devotional_bg_color' =>  $this->request->getPost('devotional_bg_color'),
                'button_bg_color' =>  $this->request->getPost('button_bg_color'),
                'button_text_color' =>  $this->request->getPost('button_text_color'),
                'export_bg_color' =>  $this->request->getPost('export_bg_color'),
                'export_text_color' =>  $this->request->getPost('export_text_color'),
                'user_color' =>  $this->request->getPost('user_color'),
                'fonts' =>  $this->request->getPost('fonts')
            );
            $session = \Config\Services::session();
            $session->set($sessiondata);
            return redirect()->to(base_url('settings.php'));
        }
        //redirect("settings",'refresh');


        $session = \Config\Services::session();
        $sessionData = $session->get();
        if(!isset($sessionData['username'])){
            return redirect()->to(base_url('/'));
        }
        $router = service('router');
        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);

        $data = [
            'cssFiles' => $cssFiles,
            'jsFiles' => $jsFiles,
            'user_data' => $sessionData,
            'class_name' =>  $class,
            'query_admin_users' => $query_admin_users
        ];

        $content = view('content/settings', $data);
        $title = [
            'title' => 'CMS: Settings',
            'content' => $content
        ];
        return view('template', $title);
    }


}
