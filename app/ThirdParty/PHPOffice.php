<?php if (!defined('BASEPATH'))	exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

//--------------------------------------------------------------------------------

/**
 * Microsoft Office intergration for Word and Excel using template files
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author Victor Angelier <vangelier@hotmail.com>
 * @copyright (c) 2012, Victor Angelier <vangelier@hotmail.com>
 * @link http://www.twitter.com/digital_human
 * 
 * Installation:
 * @todo Add a new parameter to your config.php like this: $config['office_templates_path']	= APPPATH . '/../templates';
 */
class PHPOffice 
{
	/**
	 * Holds the Controller instance
	 * 
	 * @access private
	 * @var CI_Controller
	 */
	private $CI = null;
	
	/**
	 * Holds the template we are going to work with.
	 * 
	 * @access private
	 * @var string Word or Excel template file
	 */
	private $_template = "";
	
	/**
	 * Hold the path to the template files
	 * 
	 * @access private
	 * @var string
	 */
	private $_template_path = "";
	
	/**
	 * Holds our final document
	 * 
	 * @access private
	 * @var PHPWord
	 */
	private $_document = null;
	
	/**
	 * The name and location of our temporary file
	 * 
	 * @access private
	 * @var string Fullpath including filename of our temporary file
	 */
	private $_tempfilename = "";
	
	/**
	 * Constructs the library
	 */
	public function __construct() {
		
		$this->CI =& get_instance();
	
		if(file_exists(APPPATH . "/libraries/PHPWord.php")){
			$this->CI->load->file(APPPATH . "/libraries/PHPWord.php");
		
			if(!isset($this->CI->config->config["office_templates_path"]) || $this->CI->config->config["office_templates_path"] == ""){
				log_message("error", "Office template path not found or empty. Please add a parameter 'office_templates_path' to your config.php file.");
			}else{
				if(@chdir($this->CI->config->config["office_templates_path"]) == FALSE){
					log_message("error", "Path: {$this->CI->config->config["office_templates_path"]} does not exits");
				}else{
					$this->_template_path = $this->CI->config->config["office_templates_path"]."/";
				}
			}
		}else{
			log_message("error", "PHPWord classes not found.");
		}
	}
	
	/**
	 * Sets the template file we are going to work with.
	 * 
	 * @access public
	 * @param type $filename
	 */
	public function set_template($filename = ""){
		if(file_exists($this->_template_path.$filename)){
			$this->_template = $this->_template_path.$filename;
		}else{
			log_message("error", "File {$filename} does not exists as a template file.");
		}
	}
	
	/**
	 * Set the values of the fields in the template
	 * 
	 * @access public
	 * @param array $data Key value pairs with array('field' => 'value');
	 * @return File The fullpath to the temporary file or FALSE
	 */
	public function create_document($data = array()){
		if(is_array($data) && count($data) > 0){
			
			//Now check if the data passed on is in the correct structure
			foreach($data as $keyvalues){
				if(key_exists("field", $keyvalues) && key_exists("value", $keyvalues)){				
				}else{
					log_message("error", "Invalid array structure");
					return false;
				}
			}
			
			//Seems ok, lets start the parsing
			$Word = new PHPWord();
			$this->_document = $Word->loadTemplate($this->_template);
			foreach($data as $kvp){
				$this->_document->setValue("{$kvp["field"]}", $kvp['value']);
			}
			
			//Generate a unique filename to store it for now
			$filename = APPPATH . "/../temp/" . $this->genuid() . ".docx";
			$this->_document->save($filename);
			
			//Now check if the file is really saved and return the fullpath to the user
			if(file_exists($filename)){
				$this->_tempfilename = $filename;
				return $filename;
			}else{
				log_message("error", "We could not save the file to our temporary location.");
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Get the name and location of the temporary file we just generated
	 * 
	 * @access public
	 * @return string Location and name of the temporary file or  FALSE 
	 */
	public function get_document_filename(){
		return ($this->_tempfilename == "" ? FALSE : $this->_tempfilename);
	}
	
	/**
	 * Save our temporary file to a final location
	 * 
	 * @access public
	 * @param string $filename The filename we can use
	 * @param string $path The new location of the file
	 * @return bool True or False
	 */
	public function save_document($filename = "", $path = ""){
		if($filename != "" && $path != ""){
			
			//Check if path has trailing / else fix it
			if(substr($path, strlen($path)-1) != "/"){
				$path  .= "/";
			}
			
			//Check if the file already exists
			if(file_exists($path.$filename)){
				log_message("error", "This filename is already in use. Can't save it now. Please try with a new name.");
				return false;
			}else{
				if(copy($this->_tempfilename, $path.$filename)){
					//File is copied, now we remove our temporary file
					if(unlink($this->_tempfilename)){
						log_message("error", "I'm not able to remove the temporary file");
					}
					return true;
				}else{
					return false;
				}
			}
		}else{
			log_message("error", "We did not get a filename or a path.");
			return false;
		}
	}
	
	/**
	 * Generate a unique id to use when we generate files from the templates.
	 * 
	 * @return md5 Hash to use a working filename
	 */
	private function genuid(){
		$random = range(0,100);
		shuffle($random);
		return md5(uniqid().$random);
	}
}
// END PHPOffice Class

/* End of file PHPOffice.php */
/* Location: ./libraries/PHPOffice.php */