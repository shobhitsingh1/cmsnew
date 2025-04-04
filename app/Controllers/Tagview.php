<?php

namespace App\Controllers;

use Config\Database;

use App\Models\Tags;
use App\Models\User;
use CodeIgniter\Router\Router;

class Tagview extends BaseController
{
    protected $Tags;
    protected $usersModel;


    public function __construct()
    {
        $this->Tags = new Tags();
        $this->usersModel = new User();
    }

    public function index()
    {


        $cssFiles = [
            'public/assests/css/jquery.jscrollpane.css',
        ];

        $jsFiles = [
            'public/assests/js/jquery.jscrollpane.js',
            'public/assests/js/jquery.mousewheel.js'
        ];

        $db = \Config\Database::connect();

        $data = array();
        $search_by_string = '';
        $by = array("BY" => "POST");

        if (isset($_REQUEST['View'])) {

            $WHERE = array();
            $WHERE[] = "1";

            //$this->db->order_by('id','DESC');
          
            if (!empty($_REQUEST['Search'])) {
                $temp_array = array();
                $search_by_string = str_replace(";", ",", $this->request->getPost('Search'));
                $search_by_array = explode(",", $search_by_string);
                if (count($search_by_array) > 0) {
                    foreach ($search_by_array as $search_val) {
                        if ($search_val != '') {
                            $temp_array[] = "text LIKE '%" . $search_val . "%'";
                        }

                    }

                }
                $search_by_new = implode("  OR ", $temp_array);
                $WHERE[] = " ($search_by_new) ";


            }
            if (!empty($_REQUEST['ID'])) {

                $id_by_string = $this->request->getPost('ID');
                $WHERE[] = "d.id = $id_by_string";


            }
            if (!empty($_REQUEST['date_year'])) {

                $date_by_string =  $this->request->getPost('date_year');
                $WHERE[] = "d.date_quarter  LIKE '" . $date_by_string . "'";


            }


            if (!empty($_REQUEST['date_quarter'])) {

                $date_by_string =  $this->request->getPost('date_quarter');
                $WHERE[] = "d.date_quarter  LIKE '" . $date_by_string . "'";


            }
            if (!empty($_REQUEST['from_date']) && (!empty($_REQUEST['to_date']))) {

                $from_date =  $this->request->getPost('from_date');
                $to_date =  $this->request->getPost('to_date');
                $WHERE[] = "d.devotional_date  BETWEEN '" . date("Y-m-d", strtotime($from_date)) . "' AND '" . date("Y-m-d", strtotime($to_date)) . "'";


            }



            if (count($WHERE) > 1) {
                $where_str = implode(" AND ", $WHERE);
                $sql = "SELECT d.* from `tbl_devotional` d WHERE $where_str  ORDER BY devotional_date ";
                $query_devotional = $db->query($sql)->getResult();
            } else {
                $sql = "SELECT * from tbl_tags ";
              // echo $sql;exit;
                $query_devotional = $db->query($sql)->getResult();
            }
            //echo $sql;


            $data2 = array("query_devotional" => $query_devotional, "parameter" => $search_by_string, "GET" => $_POST);


        }


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
            'class_name' =>  $class
        ];

        if(!empty($data2)){
            $data = array_merge($data ,$data2 );
        }


        $content = view('content/tagview', $data);
        $title = [
            'title' => 'CMS: Tag View',
            'content' => $content
        ];
        return view('template', $title);

    }

    // function download()
    // {
    //     $this->load->database();
    //     $this->load->model('User');
    //     $this->load->model('Tags');
    //     $WHERE = array();
    //     $WHERE[] = "1";

    //     //$this->db->order_by('id','DESC');
    //     if (!empty($_REQUEST['Search'])) {
    //         $temp_array = array();
    //         $search_by_string = str_replace(";", ",", $this->input->post('Search'));
    //         $search_by_array = explode(",", $search_by_string);
    //         if (count($search_by_array) > 0) {
    //             foreach ($search_by_array as $search_val) {
    //                 if ($search_val != '') {
    //                     $temp_array[] = "text LIKE '%" . $search_val . "%'";
    //                 }

    //             }

    //         }
    //         $search_by_new = implode("  OR ", $temp_array);
    //         $WHERE[] = " ($search_by_new) ";


    //     }
    //     if (!empty($_REQUEST['ID'])) {

    //         $id_by_string = $this->input->post('ID');
    //         $WHERE[] = "d.id = $id_by_string";


    //     }
    //     if (!empty($_REQUEST['date_year'])) {

    //         $date_by_string = substr($this->input->post('date_year'), 2, 2);
    //         $WHERE[] = "d.date_quarter  LIKE '__" . $date_by_string . "'";


    //     }


    //     if (!empty($_REQUEST['date_quarter'])) {

    //         $date_by_string = $this->input->post('date_quarter');
    //         $WHERE[] = "d.date_quarter  LIKE '" . $date_by_string . "__'";


    //     }
    //     if (!empty($_REQUEST['from_date']) && (!empty($_REQUEST['to_date']))) {

    //         $from_date = $this->input->post('from_date');
    //         $to_date = $this->input->post('to_date');
    //         $WHERE[] = "d.devotional_date  BETWEEN '" . date("Y-m-d", strtotime($from_date)) . "' AND '" . date("Y-m-d", strtotime($to_date)) . "'";


    //     }


    //     if (count($WHERE) > 0) {
    //         $where_str = implode(" AND ", $WHERE);

    //     }
    //     $tags = array();
    //     // $sql = "SELECT concat_ws(',',tag_ids,book_ids,author_ids) as tags from `tbl_devotional` d WHERE $where_str  ORDER BY devotional_date ";
    //     // $query_devotional = $this->db->query($sql);
    //     // if ($query_devotional->num_rows() > 0) {
    //     //     foreach ($query_devotional->result() as $result) {
    //     //         $tags_str = $result->tags;
    //     //         if ($tags_str != '') {
    //     //             $tag_array = explode(",", $tags_str);
    //     //             foreach ($tag_array as $v => $k) {
    //     //                 // if($k != '')
    //     //                 $tags[] = $k;


    //     //             }
    //     //         }


    //     //     }
    //     // }
    //     $db = \Config\Database::connect();
    //     $sql = "SELECT CONCAT_WS(',', tag_ids, book_ids, author_ids) AS tags 
    //             FROM tbl_devotional d 
    //             WHERE $where_str 
    //             ORDER BY devotional_date";

    //     $query = $db->query($sql);

    //     $tags = [];

    //     if ($query->getNumRows() > 0) {
    //         foreach ($query->getResult() as $row) {
    //             $tags_str = $row->tags;
    //             if (!empty($tags_str)) {
    //                 $tag_array = explode(",", $tags_str);
    //                 foreach ($tag_array as $tag) {
    //                     if (!empty($tag)) {
    //                         $tags[] = $tag;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     $csv = array();

    //     if (count($tags) > 0) {
    //         $final_tags = array_count_values($tags);
    //         //print_r( $final_tags);
    //         if (count($final_tags) > 0) {
    //             $i = 0;
    //             $c1['tag_name'] = "TAG NAME";
    //             $c1['count'] = "Count";
    //             $c1['tag_type'] = "Tag Type";
    //             $c1['search'] = "Search";
    //             $c1['year'] = "Year";
    //             $c1['date_quarter'] = "Date Quarter";
    //             $c1['from_date'] = "From Date";
    //             $c1['to_date'] = "To Date";
    //             $csv[] = $c1;
    //             foreach ($final_tags as $k => $v) {
    //                 $c = array();
    //                 if ($k != '') {
    //                     $c['tag_name'] = $this->Tags->getTagNameById($k);
    //                     $c['count'] = $v;
    //                     $c['tag_type'] = $this->Tags->getTagTypeById($k);
    //                     if ($i == 0) {
    //                         $c['search'] = ($this->input->post('Search') != '') ? $this->input->post('Search') : '';
    //                         $c['year'] = ($this->input->post('date_year') != '') ? $this->input->post('Search') : '';
    //                         $c['date_quarter'] = ($this->input->post('date_quarter') != '') ? $this->input->post('Search') : '';
    //                         $c['from_date'] = ($this->input->post('from_date') != '') ? $this->input->post('Search') : '';
    //                         $c['to_date'] = ($this->input->post('tp_date') != '') ? $this->input->post('Search') : '';

    //                     } else {
    //                         $c['search'] = '';
    //                         $c['year'] = '';
    //                         $c['date_quarter'] = '';
    //                         $c['from_date'] = '';
    //                         $c['to_date'] = '';


    //                     }
    //                     $i++;
    //                     $csv[] = $c;

    //                 }
    //             }


    //         }

    //     }

    //     // print "<pre>";
    //     // print_r($csv);
    //     $fichier = 'devotional_tags.csv';
    //     header("Content-Type: text/csv;charset=utf-8");
    //     header("Content-Disposition: attachment;filename=\"$fichier\"");
    //     header("Pragma: no-cache");
    //     header("Expires: 0");

    //     $fp = fopen('php://output', 'w');

    //     foreach ($csv as $fields) {
    //         @fputcsv($fp, $fields);
    //     }
    //     fclose($fp);
    //     exit();


    // }

    public function download()
    {
        helper(['url', 'form']);

        $db = \Config\Database::connect();
        $Tags = new Tags();

        $request = \Config\Services::request();
        $WHERE = ['1'];

        // Search
        $search = $request->getPost('Search');
        if (!empty($search)) {
            $search_by_string = str_replace(";", ",", $search);
            $search_by_array = explode(",", $search_by_string);
            $temp_array = [];

            foreach ($search_by_array as $search_val) {
                $search_val = trim($search_val);
                if ($search_val !== '') {
                    $temp_array[] = "text LIKE '%" . $db->escapeLikeString($search_val) . "%'";
                }
            }

            if (!empty($temp_array)) {
                $WHERE[] = '(' . implode(' OR ', $temp_array) . ')';
            }
        }

        // ID
        if (!empty($request->getPost('ID'))) {
            $id_by_string = $request->getPost('ID');
            $WHERE[] = "d.id = " . (int)$id_by_string;
        }

        // Year
        if (!empty($request->getPost('date_year'))) {
            $date_by_string = substr($request->getPost('date_year'), 2, 2);
            $WHERE[] = "d.date_quarter LIKE '__" . $date_by_string . "'";
        }

        // Quarter
        if (!empty($request->getPost('date_quarter'))) {
            $date_by_string = $request->getPost('date_quarter');
            $WHERE[] = "d.date_quarter LIKE '" . $db->escapeLikeString($date_by_string) . "__'";
        }

        // Date Range
        if (!empty($request->getPost('from_date')) && !empty($request->getPost('to_date'))) {
            $from_date = date("Y-m-d", strtotime($request->getPost('from_date')));
            $to_date = date("Y-m-d", strtotime($request->getPost('to_date')));
            $WHERE[] = "d.devotional_date BETWEEN '$from_date' AND '$to_date'";
        }

        $where_str = implode(" AND ", $WHERE);

        // Query to get tag data
        $sql = "SELECT CONCAT_WS(',', tag_ids, book_ids, author_ids) AS tags 
                FROM tbl_devotional d 
                WHERE $where_str 
                ORDER BY devotional_date";

        $query = $db->query($sql);
        $tags = [];

        foreach ($query->getResult() as $row) {
            if (!empty($row->tags)) {
                $tag_array = explode(",", $row->tags);
                foreach ($tag_array as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        $csv = [];
        if (!empty($tags)) {
            $final_tags = array_count_values($tags);
            if (!empty($final_tags)) {
                $csv[] = [
                    'tag_name' => 'TAG NAME',
                    'count' => 'Count',
                    'tag_type' => 'Tag Type',
                    'search' => 'Search',
                    'year' => 'Year',
                    'date_quarter' => 'Date Quarter',
                    'from_date' => 'From Date',
                    'to_date' => 'To Date'
                ];

                $i = 0;
                foreach ($final_tags as $k => $v) {
                    $row = [];
                    $row['tag_name'] = $Tags->getTagNameById($k);
                    $row['count'] = $v;
                    $row['tag_type'] = $Tags->getTagTypeById($k);

                    if ($i === 0) {
                        $row['search'] = $search ?? '';
                        $row['year'] = $request->getPost('date_year') ?? '';
                        $row['date_quarter'] = $request->getPost('date_quarter') ?? '';
                        $row['from_date'] = $request->getPost('from_date') ?? '';
                        $row['to_date'] = $request->getPost('to_date') ?? '';
                    } else {
                        $row['search'] = '';
                        $row['year'] = '';
                        $row['date_quarter'] = '';
                        $row['from_date'] = '';
                        $row['to_date'] = '';
                    }

                    $csv[] = $row;
                    $i++;
                }
            }
        }

        // Output CSV
        $filename = 'devotional_tags.csv';
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $fp = fopen('php://output', 'w');
        foreach ($csv as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        exit();
    }
}
