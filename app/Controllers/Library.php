<?php

namespace App\Controllers;

use Config\Database;

use App\Models\Tags;
use App\Models\User;
use CodeIgniter\Router\Router;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class Library extends BaseController
{

    protected $tagsModel;
    protected $usersModel;

    public function __construct()
    {
        $this->tagsModel = new Tags();
        $this->usersModel = new User();
    }

    public function index()
    {

        $cssFiles = [
            'public/assests/css/jquery.multiselect_new.css',
            'public/assests/css/jquery.multiselect.filter.css',
            'public/assests/css/colorbox.css',
        ];

        // Load JS files
        $jsFiles = [
            'public/assests/js/jquery.multiselect_new.js',
            'public/assests/js/jquery.multiselect.filter.js',
            'public/assests/js/jquery.colorbox.js',
            'public/assests/js/jquery.lavalamp.min.js',
        ];

        $db = \Config\Database::connect();

        $data = array();
        $search_by_string = '';

        //print_r($_REQUEST);
        //$this->template->view('template');

        if (isset($_REQUEST['View'])) {

//			$this->load->model('User');
//			$this->load->model('Tags');
            $WHERE = array();
            $WHERE[] = "1";

            //$this->db->order_by('id','DESC');
            if (!empty($_REQUEST['Search'])) {
                $temp_array = array();
                $search_by_string = str_replace(";", ",", $this->request->getPost('Search'));
                if (strpos($search_by_string, "&") > 0) {
                    $search_by_array = explode("&", $search_by_string);
                    //$search_by_array_2 = str_replace(array(",",'"'),array(" ",''),$search_by_string);
                    if (count($search_by_array) > 0) {
                        foreach ($search_by_array as $search_val) {
                            if ($search_val != '') {
                                if (strpos($search_val, '"', 1)) {
                                    $temp_array[] = "text RLIKE '[[:<:]]" . str_replace('"', '', $search_val) . "[[:>:]]'";

                                } else {
                                    $temp_array[] = "text LIKE '%" . $search_val . "%'";
                                }
                            }

                        }

                    }
                    $search_by_new = implode("  AND ", $temp_array);

                } else {
                    $search_by_array = explode(",", $search_by_string);
                    //$search_by_array_2 = str_replace(array(",",'"'),array(" ",''),$search_by_string);
                    if (count($search_by_array) > 0) {
                        foreach ($search_by_array as $search_val) {
                            if ($search_val != '') {
                                if (strpos($search_val, '"', 1)) {
                                    $temp_array[] = "text RLIKE '[[:<:]]" . str_replace('"', '', $search_val) . "[[:>:]]'";

                                } else {
                                    $temp_array[] = "text LIKE '%" . $search_val . "%'";
                                }
                            }

                        }

                    }
                    $search_by_new = implode("  OR ", $temp_array);
                }
                /* if(!empty($search_by_array_2)){
                     $temp_array[] = "text LIKE '%".$search_by_array_2."%'";
                 }*/

                $WHERE[] = " ($search_by_new) ";


            }

            if (!empty($_REQUEST['ack_search'])) {


                //$WHERE[] = "d.acknowledgements like '%$ack_search%'";

                $temp_array = array();
                $ack_search = $this->request->getPost('ack_search');
                if (strpos($ack_search, "&") > 0) {
                    $search_by_array = explode("&", $ack_search);
                    //$search_by_array_2 = str_replace(array(",",'"'),array(" ",''),$search_by_string);
                    if (count($search_by_array) > 0) {
                        foreach ($search_by_array as $search_val) {
                            if ($search_val != '') {
                                if (strpos($search_val, '"', 1)) {
                                    $temp_array[] = "acknowledgements RLIKE '[[:<:]]" . str_replace('"', '', $search_val) . "[[:>:]]'";

                                } else {
                                    $temp_array[] = "acknowledgements LIKE '%" . $search_val . "%'";
                                }
                            }

                        }

                    }
                    $search_by_new = implode("  AND ", $temp_array);

                } else {
                    $search_by_array = explode(",", $ack_search);
                    //$search_by_array_2 = str_replace(array(",",'"'),array(" ",''),$search_by_string);
                    if (count($search_by_array) > 0) {
                        foreach ($search_by_array as $search_val) {
                            if ($search_val != '') {
                                if (strpos($search_val, '"', 1)) {
                                    $temp_array[] = "acknowledgements RLIKE '[[:<:]]" . str_replace('"', '', $search_val) . "[[:>:]]'";

                                } else {
                                    $temp_array[] = "acknowledgements LIKE '%" . $search_val . "%'";
                                }
                            }

                        }

                    }
                    $search_by_new = implode("  OR ", $temp_array);
                }
                /* if(!empty($search_by_array_2)){
                     $temp_array[] = "text LIKE '%".$search_by_array_2."%'";
                 }*/

                $WHERE[] = " ($search_by_new) ";


            }
            if (!empty($_REQUEST['ID'])) {

                $id_by_string = $this->request->getPost('ID');

                $WHERE[] = "d.id IN ($id_by_string)";


            }
            if (!empty($_REQUEST['date_year'])) {

                $date_by_string = $this->request->getPost('date_year');
                $WHERE[] = "d.date_year  LIKE '" . $date_by_string . "'";


            }


            if (!empty($_REQUEST['date_quarter'])) {

                $date_by_string = $this->request->getPost('date_quarter');
                $WHERE[] = "d.date_quarter  LIKE '" . $date_by_string . "'";


            }
            if (!empty($_REQUEST['from_date']) && (!empty($_REQUEST['to_date']))) {

                $from_date = $this->request->getPost('from_date');
                $to_date = $this->request->getPost('to_date');
                $WHERE[] = "d.devotional_date  BETWEEN '" . date("Y-m-d", strtotime($from_date)) . "' AND '" . date("Y-m-d", strtotime($to_date)) . "'";


            }

            if (isset($_REQUEST['Tags'])) {
                if (count($_REQUEST['Tags']) > 0) {
                    $tags_array = $this->request->getPost('Tags');
                    $WHERE_FIELD = array();
                    foreach ($tags_array as $val) {
                        $WHERE_FIELD[] = "  FIND_IN_SET($val, d.tag_ids)";
                    }
                    $WHERE[] = "(" . implode(" OR ", $WHERE_FIELD) . ")";
                    //$WHERE[] = "d.tag_ids FIND_IN_SET (".implode(",",$tags_array).")";


                }


            }
            if (isset($_REQUEST['author'])) {
                if (count($_REQUEST['author']) > 0) {
                    $author_array = $this->request->getPost('author');
                    //$WHERE[] = "d.author_ids IN (".implode(",",$author_array).")";
                    $WHERE_FIELD = array();
                    foreach ($author_array as $val) {
                        $WHERE_FIELD[] = "  FIND_IN_SET($val, d.author_ids)";
                    }
                    $WHERE[] = "(" . implode(" OR ", $WHERE_FIELD) . ")";

                }


            }
            if (isset($_REQUEST['books'])) {
                if (count($_REQUEST['books']) > 0) {
                    $books_array = $this->request->getPost('books');
                    //$WHERE[] = "d.book_ids IN (".implode(",",$books_array).")";
                    $WHERE_FIELD = array();
                    foreach ($books_array as $val) {
                        $WHERE_FIELD[] = "  FIND_IN_SET($val, d.book_ids)";
                    }
                    $WHERE[] = "(" . implode(" OR ", $WHERE_FIELD) . ")";


                }


            }
            if (count($WHERE) > 0) {
                $where_str = implode(" AND ", $WHERE);

            }

            /* $this->load->library('pagination');
             $uri = $this->uri->uri_to_assoc(2);
            print_r($uri);
             $config['per_page'] = 5;
             $config['use_page_numbers'] = TRUE;
             $config['uri_segment'] = true;
             //$config['page_query_string'] = TRUE;
             $per_page = 0;
             echo $this->uri->segment(2);
             if(ISSET($_REQUEST['page'])){
                 $per_page = ($this->uri->segment(1) > 0)?$this->uri->segment(1):0;
                 $st = $per_page;

             }else{
                 $st =  0;
             }
             $limit = "LIMIT ".$st.", ".$config['per_page'];*/
            $order_by = " ORDER BY id  ";
            if (isset($_REQUEST['sortby'])) {
                if ($_POST['sortby'] != 'counter' && $_POST['sortby'] != '') {
                    $asc_by = " " . $this->request->getPost('sortedBy');
                    $order_by = " ORDER BY " . $this->request->getPost('sortby') . " " . $asc_by;

                }

            }
            //$sql = "SELECT d.* from `tbl_devotional` d WHERE $where_str  ORDER BY devotion_date ".$limit;
            $sql = "SELECT d.* FROM tbl_devotional d WHERE $where_str $order_by";
            $query_devotional = $db->query($sql)->getResult();

            /* $sql_counter = "SELECT d.* from `tbl_devotional` d WHERE $where_str ";

             $query_counter_devotional  = $this->db->query($sql_counter);
             $search_array = array();*/
            //print_r($_SERVER);
            /*if(count($_REQUEST) > 0){
                foreach($_REQUEST as $get_key => $get_val){
                       if(is_array($get_val))
                        $get_val = implode(",",$get_val);
                        if($get_key != 'per_page')
                       $search_array[] = $get_key."=".$get_val;
                
                }
            
            }*/
            //$counter_var = (ISSET($_REQUEST['counter']))?$_REQUEST['counter']:0;
            // echo $query_devotional->num_rows();
            // $search_str = implode("&", $search_array);

            $data2 = array("query_devotional" => $query_devotional, "parameter" => $search_by_string, "GET" => $_POST);
            //$config['base_url'] = base_url() . 'library/index';;
            //$config['uri_segment'] = 3;
            // $config['base_url'] = base_url()."library.php/page/";
            // $config['total_rows'] = $query_counter_devotional->num_rows();
            // unset($uri['page']); // Don't needed
            // $config['suffix'] = !empty($uri) ? '/'. $this->uri->assoc_to_uri($uri) : '';
            // $this->pagination->initialize($config);

        }


//        $sessionData = session()->get();
//        print_r($sessionData);exit;

        $query = $this->tagsModel->where('type', 'Tags')
            ->orderBy('title', 'ASC')->get();

        $query_tags = $query->getResultObject();

        $query_books = $this->tagsModel->where('type', 'Books')
            ->orderBy('title', 'ASC')
            ->get();

        $query_books = $query_books->getResultObject();

        $query_author = $this->tagsModel->where('type', 'Author')
            ->orderBy('title', 'ASC')
            ->get();

        $query_author = $query_author->getResultObject();;


//        $this->template->setTitle('CMS: Library');
//        $this->template->set('query_tags', $query_tags);
//        $this->template->set('query_books', $query_books);
//        $this->template->set('query_author', $query_author);
//        $data = array_merge($data,array("query_tags" =>$query_tags,"query_books" => $query_books,"query_author" => $query_author));
//        echo '<pre>';print_r($data);exit;
        $session = \Config\Services::session();
        $sessionData = $session->get();
        $router = service('router');

        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);


        $data = [
            'cssFiles' => $cssFiles,
            'jsFiles' => $jsFiles,
            'query_tags' => $query_tags,
            'query_books' => $query_books,
            'query_author' => $query_author,
            'user_data' => $sessionData,
            'class_name' => $class
        ];

        if (!empty($data2)) {
            $data = array_merge($data, $data2);
        }


        $content = view('content/library', $data);
        $title = [
            'title' => 'CMS: Library',
            'content' => $content
        ];
        return view('template', $title);
    }

    function download()
    {

        $db = \Config\Database::connect();

        ///http://www.ahowto.net/php/creating-ms-word-document-using-codeigniter-and-phpword

        if (isset($_POST['checkBoxList'])) {
            $checkBoxList = explode(",", $this->request->getPost('checkBoxList'));
            $group1 = $this->request->getPost('group1');
            $group2 = $this->request->getPost('group2');
            $group3 = $this->request->getPost('group3');

            $query = $db->table('tbl_devotional');

            if (isset($_REQUEST['sortby']) && $_REQUEST['sortby'] != 'undefined') {
                $query->orderBy($this->request->getPost('sortby'), $this->request->getPost('sortedBy'));
            }

            $query->whereIn('id', $checkBoxList)
                ->limit(30);

            // Execute query
            $query_data = $query->get()->getResultObject();

            if (count($query_data) > 0) {
                $phpWord = new PhpWord();
                $section = $phpWord->addSection();

                // Loop through query results
                foreach ($query_data as $rows) {
                    // Add devotional date
                    $dateText = ($group3 == 'HID') ? "" : "(ID " . $rows->id . ") ";
                    $dateText .= html_entity_decode(date("l F d, Y", strtotime($rows->devotional_date)), ENT_COMPAT, 'UTF-8');
                    $section->addText($dateText, '', ['spaceAfter' => '1']);
                    $section->addText(html_entity_decode($rows->title, ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                    $section->addText(html_entity_decode($rows->subtitle, ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                    $section->addText(html_entity_decode($rows->text, ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);

                    if ($group1 != 'HT') {
                        $section->addText("Tags: " . html_entity_decode($this->tagsModel->getTagsName($rows->tag_ids), ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                        $section->addText("Books: " . html_entity_decode($this->tagsModel->getTagsName($rows->book_ids), ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                        $section->addText("Authors: " . html_entity_decode($this->tagsModel->getTagsName($rows->author_ids), ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                    }

                    if ($group2 != 'HA') {
                        $section->addText("Acknowledgements: " . html_entity_decode($rows->acknowledgements, ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                    }

                    $section->addText("Submitted By: " . html_entity_decode($this->usersModel->getUserName($rows->user_id), ENT_COMPAT, 'UTF-8'), '', ['spaceAfter' => '1']);
                    $section->addTextBreak(1);
                }

                // Prepare and output the Word document
                $filename = 'your_desired_filename.docx';
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                $writer = IOFactory::createWriter($phpWord, 'Word2007');
                $writer->save('php://output');
                exit;
            }
        }
        return redirect()->to(base_url('library.php'));

    }

    function librarysingle()
    {

        $db = \Config\Database::connect();
        $session = \Config\Services::session();
        $user_data = $session->get();

        $id = $this->request->getGet('id');


        $database = Database::connect();

        $query_devotional = $db->table('tbl_devotional')
            ->select('*')
            ->where('id', $id)
            ->get();
        $query_devotional = $query_devotional->getResultObject();


        $query_tags = $this->tagsModel
            ->select('*')
            ->where('type', 'Tags')
            ->orderBy('title', 'ASC')
            ->get();

        $query_tags = $query_tags->getResultObject();

        $query_books = $this->tagsModel
            ->select('*')
            ->where('type', 'Books')
            ->orderBy('title', 'ASC')
            ->get();

        $query_books = $query_books->getResultObject();

        $query_author = $this->tagsModel
            ->select('*')
            ->where('type', 'Author')
            ->orderBy('title', 'ASC')
            ->get();

        $query_author = $query_author->getResultObject();


        $session = \Config\Services::session();
        $sessionData = $session->get();
        $router = service('router');

        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);
        $data = [
            'query_tags' => $query_tags,
            'query_books' => $query_books,
            'query_author' => $query_author,
            'user_data' => $sessionData,
            'class_name' => '',
            'query_devotional' => $query_devotional
        ];

        return view('content/librarysingle', $data);
    }

    function libraryseries()
    {
        $db = \Config\Database::connect();
        $session = \Config\Services::session();
        $user_data = $session->get();

        $id = $this->request->getGet('id');


        $database = Database::connect();

        $query_devotional = $db->table('tbl_devotional')
            ->select('*')
            ->where('series_id', $id)
            ->get();
        $query_devotional = $query_devotional->getResultObject();


        $query_tags = $this->tagsModel
            ->select('*')
            ->where('type', 'Tags')
            ->orderBy('title', 'ASC')
            ->get();

        $query_tags = $query_tags->getResultObject();

        $query_books = $this->tagsModel
            ->select('*')
            ->where('type', 'Books')
            ->orderBy('title', 'ASC')
            ->get();

        $query_books = $query_books->getResultObject();

        $query_author = $this->tagsModel
            ->select('*')
            ->where('type', 'Author')
            ->orderBy('title', 'ASC')
            ->get();

        $query_author = $query_author->getResultObject();


        $session = \Config\Services::session();
        $sessionData = $session->get();
        $router = service('router');

        $class = $router->controllerName();
        $className = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower($className);
        $data = [
            'query_tags' => $query_tags,
            'query_books' => $query_books,
            'query_author' => $query_author,
            'user_data' => $sessionData,
            'class_name' => '',
            'query_devotional' => $query_devotional
        ];
        return view('content/libraryseries', $data);


    }
}
