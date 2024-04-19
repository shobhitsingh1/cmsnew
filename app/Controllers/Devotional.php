<?php

namespace App\Controllers;

use Config\Database;

use App\Models\Tags;
use App\Models\User;
use CodeIgniter\Router\Router;

class Devotional extends BaseController
{

    protected $tagsModel;
    protected $usersModel;


    public function __construct()
    {
        $this->tagsModel = new Tags();
        $this->usersModel = new User();
    }


    public function init()
    {

    }

    public function index()
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
        ];

        $database = Database::connect();

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

        $db = \Config\Database::connect();

        $query_devotional = $db->table('tbl_devotional')
            ->select('*')
            ->orderBy('created_on', 'DESC')
            ->limit(30)
            ->get();

        $query_devotional = $query_devotional->getResultObject();

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
            'query_tags' => $query_tags,
            'query_books' => $query_books,
            'query_author' => $query_author,
            'session_data' => $sessionData,
            'class_name' => $class,
            'query_devotional' => $query_devotional
        ];

        $content = view('content/adddevotional', $data);
        $title = [
            'title' => 'CMS: Add Devotional',
            'content' => $content,
            'user_data' => $sessionData
        ];
        return view('template', $title);
    }

    public function addtag()
    {
//        $this->load->database();
//        $tag_name = $this->input->post('tag_name');
//        $tag_type = $this->input->post('tag_type');

        $db = \Config\Database::connect();
        $tag_name = $this->request->getPost('tag_name');
        $tag_type = $this->request->getPost('tag_type');

//
//        $this->tagsModel->select('*');
//        $this->tagsModel->where('type', $tag_type);
//        $this->tagsModel->where('title', $tag_name);
//        $this->tagsModel->from('tbl_tags');

        $query_tags = $this->tagsModel
            ->select('*')
            ->where('type', $tag_type)
            ->where('title', $tag_name)
            ->get();


        $query_exist_tags = $query_tags->getResultObject();


        if (empty($query_exist_tags)) {
            $data = array(
                'title' => $tag_name,
                'type' => $tag_type,
                'created_on' => date("Y-m-d H:i:s")
            );

            $builder = $db->table('tbl_tags');

            $builder->insert($data);
            $id =  $db->insertID();

            echo $id;

    } else {
            echo "EXIST";

        }


    }

    function adddevotional()
    {
        $this->load->database();


        $arrMonth = array();

        $arrMonth[0] = "January";
        $arrMonth[1] = "February";
        $arrMonth[2] = "March";
        $arrMonth[3] = "April";
        $arrMonth[4] = "May";
        $arrMonth[5] = "June";
        $arrMonth[6] = "July";
        $arrMonth[7] = "August";
        $arrMonth[8] = "September";
        $arrMonth[9] = "October";
        $arrMonth[10] = "November";
        $arrMonth[11] = "December";
        $strText = ltrim($_POST["devotional"]);
        $user_data = $this->session->all_userdata();

        //print_r($_POST);
        if ($strText != '') {

            $strText = str_replace(chr(13) . chr(10) . chr(32) . chr(13) . chr(10), chr(13) . chr(10) . chr(13) . chr(10), $strText);
            //window
            $osType = getOs();
            if ($osType == 'WIN')
                $arrText = @split(chr(13) . chr(10) . chr(13) . chr(10), $strText);
            else
                $arrText = explode("\n\n", $strText);

            //print_r($arrText); die;
            foreach ($arrText as $key => $value) {

                $strText = $value;

                if ($strText != "") {
                    $x = chr(13) . chr(10);
                    if ($osType == 'WIN')
                        $arrListing = @split($x, $strText);
                    else
                        $arrListing = explode("\n", $strText);

                    //print_r($arrListing); die;
                    if (count($arrListing) >= 3) {

                        $strDate = trim($arrListing[0]);
                        $str_date_arr = explode(" ", $strDate);
                        $strDay = trim($str_date_arr[0]);
                        $str_month = trim($str_date_arr[1]);
                        $str_day = trim($str_date_arr[2]);

                        $next_year = date("Y") + 1;
                        $previous_year = date("Y") - 3;
                        $tmp_year = date("Y");

                        /*for($z=$next_year;$z>=$previous_year;$z--)
                        {
                            $tmp_week_day = date("l",strtotime($str_day. "-" . $str_month. "-" . $z));
                            if($tmp_week_day == $strDay)
                            {
                                $tmp_year = $z;
                                break;
                            }

                        }*/
                        $tmp_year = $_POST['date_devo'];
                        //$strDate  = date("md",strtotime($str_day. "-" . $str_month. "-" . $tmp_year));
                        //$currentDate = date("md");
                        /*if($currentDate > $strDate){
                        //if(strtotime(date("Y-m-d")) > strtotime($strDate)){
                            $strDate  = date("Y-m-d",strtotime($str_day. "-" . $str_month. "-" . $next_year));
                        }else{
                            $strDate  = date("Y-m-d",strtotime($str_day. "-" . $str_month. "-" . $tmp_year));
                        }*/
                        $strDate = date("Y-m-d", strtotime($str_day . "-" . $str_month . "-" . $tmp_year));
                        $final_date = trim($strDate);

                        if (isValidDate($final_date) == 1) {
                            $strDate = $strDate;
                        } else {
                            $strDate = "null";
                        }


                        $strTitle = $arrListing[1];
                        $strSubtitle = $arrListing[2];

                        $strText = "";

                        for ($x = 3; $x < count($arrListing); $x++) {
                            $strText .= $arrListing[$x];
                        }

                        $strText = $strText;
                        /* $strText = str_replace(chr(176),"&deg;",
                        //str_replace(".".chr(153),"&trade;",
                        str_replace(chr(151),"-",
                        str_replace(chr(150),"-",
                        str_replace(chr(149),"&middot;",
                        str_replace(chr(148),'"',
                        str_replace(chr(147),'"',
                        str_replace(chr(146),"'",
                        str_replace(chr(145),"'",
                        str_replace(chr(133),"...",
                        str_replace(chr(132),'"',
                        str_replace(chr(130),",",$strText)))))))))));*/


                        //echo "--------------------------Text start here-------";
                        $strText = str_replace(chr(130), ",", $strText);
                        //echo $strText."--------------Text Ends here-------------------"."<br>";

                        //echo "---------Title starts here------------------------"."<br>";


                        $strTitle = str_replace(chr(176), "&deg;",
                            //str_replace(chr(153),"&trade;",
                            str_replace(chr(151), "-",
                                str_replace(chr(150), "-",
                                    str_replace(chr(149), "&middot;",
                                        str_replace(chr(148), '"',
                                            str_replace(chr(147), '"',
                                                str_replace(chr(146), "'",
                                                    str_replace(chr(145), "'",
                                                        str_replace(chr(133), "...",
                                                            str_replace(chr(132), '"',
                                                                str_replace(chr(130), ",", $strTitle)))))))))));


                        //echo $strTitle."----------- Title Ends here----------------------"."<br>";

                        //echo "-----------Sub Title Starts here----------------------"."<br>";


                        $strSubtitle = str_replace(chr(176), "&deg;",
                            //str_replace(chr(153),"&trade;",
                            str_replace(chr(151), "-",
                                str_replace(chr(150), "-",
                                    str_replace(chr(149), "&middot;",
                                        str_replace(chr(148), '"',
                                            str_replace(chr(147), '"',
                                                str_replace(chr(146), "'",
                                                    str_replace(chr(145), "'",
                                                        str_replace(chr(133), "...",
                                                            str_replace(chr(132), '"',
                                                                str_replace(chr(130), ",", $strSubtitle)))))))))));

                        $strSubtitle . "----------------Sub Title End here-----------------" . "<br>";

                        if (isValidDate($strDate) == 1) {

                            if (empty($_POST['series_processing'])) {

                                $this->tagsModel->select('*');
                                $this->tagsModel->where('devotional_date', $strDate);
                                $this->tagsModel->from('tbl_devotional');
                                $query_devotional_date = $this->tagsModel->get();
                                $count_devotinal_date = $query_devotional_date->num_rows();
                                if ($count_devotinal_date == 0) {
                                    $data = array(
                                        'title' => htmlentities(($strTitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'subtitle' => htmlentities(($strSubtitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'text' => htmlentities(($strText), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'acknowledgements' => htmlentities(($_POST['acknowledgements']), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'date_year' => ($_POST['date_devo']),
                                        'date_quarter' => ($_POST['date_quarter']),
                                        'tag_ids' => @implode(",", ($_POST['Tags'])),
                                        'book_ids' => @implode(",", ($_POST['books'])),
                                        'author_ids' => @implode(",", ($_POST['author'])),
                                        'devotional_date' => $strDate,
                                        'user_id' => $user_data['username_id'],
                                        'created_on' => date("Y-m-d H:i:s"),

                                    );
                                    //print_r($data); die;
                                    $this->tagsModel->insert('tbl_devotional', $data);
                                } else {
                                    $data = array(
                                        'title' => htmlentities(($strTitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'subtitle' => htmlentities(($strSubtitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'text' => htmlentities(($strText), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'acknowledgements' => htmlentities(($_POST['acknowledgements']), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'date_year' => ($_POST['date_devo']),
                                        'date_quarter' => ($_POST['date_quarter']),
                                        'tag_ids' => @implode(",", ($_POST['Tags'])),
                                        'book_ids' => @implode(",", ($_POST['books'])),
                                        'author_ids' => @implode(",", ($_POST['author'])),
                                        'user_id' => $user_data['username_id'],
                                        'created_on' => date("Y-m-d H:i:s")
                                    );


                                    $this->tagsModel->where('devotional_date', $strDate);
                                    $this->tagsModel->update('tbl_devotional', $data);


                                }


                            } else {

                                if (!empty($_POST['series_processing']) && !empty($_POST['start_processing'])) {

                                    $series_id = 1;
                                    $this->tagsModel->select('MAX(series_id) as max_series');
                                    $this->tagsModel->from('tbl_devotional');
                                    $series_sql = $this->tagsModel->get();
                                    $series_sql->num_rows();

                                    if ($series_sql->num_rows() > 0) {
                                        $series_row = $series_sql->row();
                                        //print_r($series_row);
                                        $series_id_exist = $series_row->max_series;
                                        $series_id = $series_id_exist + 1;
                                    }


                                    $this->session->set_userdata('series_processing', $_POST['series_processing']);
                                    $this->session->set_userdata('start_processing', $_POST['start_processing']);
                                    //checking if devotional date not exist


                                    $data = array(
                                        'title' => htmlentities(($strTitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'subtitle' => htmlentities(($strSubtitle), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'text' => htmlentities(($strText), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'acknowledgements' => htmlentities(($_POST['acknowledgements']), ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                        'date_year' => ($_POST['date_devo']),
                                        'date_quarter' => ($_POST['date_quarter']),
                                        'tag_ids' => @implode(",", ($_POST['Tags'])),
                                        'book_ids' => @implode(",", ($_POST['books'])),
                                        'author_ids' => @implode(",", ($_POST['author'])),
                                        'devotional_date' => $strDate,
                                        'created_on' => date("Y-m-d H:i:s"),
                                        'user_id' => $user_data['username_id'],
                                        'series_id' => $series_id

                                    );
                                    //print_r($data); die;
                                    $this->tagsModel->insert('tbl_devotional_tmp', $data);
                                    if ($_POST['start_processing'] == $_POST['series_processing']) {
                                        $series_last_id = $_POST['series_processing'];
                                        for ($i = 1; $i <= $series_last_id; $i++) {

                                            $this->tagsModel->select('*');
                                            $this->tagsModel->from('tbl_devotional_tmp');
                                            //$this->db->where('series_id',$i);
                                            $this->tagsModel->where('user_id', $user_data['username_id']);
                                            $query_devotional_tmp = $this->tagsModel->get();
                                            if ($query_devotional_tmp->num_rows() > 0) {
                                                foreach ($query_devotional_tmp->result() as $row_devotional_obj) {
                                                    $row_devotional = (array)$row_devotional_obj;
                                                    //Strdata already in DB
                                                    $this->tagsModel->select('*');
                                                    $this->tagsModel->where('devotional_date', $row_devotional['devotional_date']);
                                                    $this->tagsModel->from('tbl_devotional');
                                                    $query_devotional_date = $this->tagsModel->get();
                                                    $count_devotinal_date = $query_devotional_date->num_rows();
                                                    if ($count_devotinal_date == 0) {


                                                        $data = array(
                                                            'title' => $row_devotional['title'],
                                                            'subtitle' => $row_devotional['subtitle'],
                                                            'text' => $row_devotional['text'],
                                                            'acknowledgements' => $row_devotional['acknowledgements'],
                                                            'date_year' => $row_devotional['date_year'],
                                                            'date_quarter' => $row_devotional['date_quarter'],
                                                            'tag_ids' => $row_devotional['tag_ids'],
                                                            'book_ids' => $row_devotional['book_ids'],
                                                            'author_ids' => $row_devotional['author_ids'],
                                                            'devotional_date' => $row_devotional['devotional_date'],
                                                            'created_on' => $row_devotional['created_on'],
                                                            'series_id' => $row_devotional['series_id'],
                                                            'user_id' => $user_data['username_id']
                                                        );
                                                        //print_r($data); die;
                                                        $this->tagsModel->insert('tbl_devotional', $data);

                                                    } else {
                                                        $data = array(
                                                            'title' => $row_devotional['title'],
                                                            'subtitle' => $row_devotional['subtitle'],
                                                            'text' => $row_devotional['text'],
                                                            'acknowledgements' => $row_devotional['acknowledgements'],
                                                            'date_year' => $row_devotional['date_year'],
                                                            'date_quarter' => $row_devotional['date_quarter'],
                                                            'tag_ids' => $row_devotional['tag_ids'],
                                                            'book_ids' => $row_devotional['book_ids'],
                                                            'author_ids' => $row_devotional['author_ids'],
                                                            //  'devotional_date' => $row_devotional['devotional_date'],
                                                            'created_on' => $row_devotional['created_on'],
                                                            'series_id' => $row_devotional['series_id'],
                                                            'user_id' => $user_data['username_id']
                                                        );
                                                        $this->tagsModel->where('devotional_date', $row_devotional['devotional_date']);
                                                        $this->tagsModel->update('tbl_devotional', $data);


                                                    }


                                                }
                                            }


                                        }
                                        $array_items = array('series_processing' => null, 'start_processing' => null);

                                        $this->session->unset_userdata($array_items);


                                    }


                                }

                            }


                        }

                    }
                }
            }


        }
        redirect(get_full_url() . "add_devotional.php");
        //$this->template->render();


    }


    function devotionaltags()
    {
        $this->load->database();
        $tag_ids = $this->input->post('tag_ids');
        $author_ids = $this->input->post('author_ids');
        $books_ids = $this->input->post('books_ids');
        $devotional_id = $this->input->post('devotional_id');
        $data = array("tag_ids" => @implode(",", $tag_ids), "book_ids" => @implode(",", $books_ids), "author_ids" => @implode(",", $author_ids));
        $this->tagsModel->where('id', $devotional_id);
        $this->tagsModel->update('tbl_devotional', $data);

        echo "ok";


    }

}

?>