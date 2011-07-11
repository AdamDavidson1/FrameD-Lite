<?php 
/*
 ______   ______     ______     __    __     ______     _____    
/\  ___\ /\  == \   /\  __ \   /\ "-./  \   /\  ___\   /\  __-.  
\ \  __\ \ \  __<   \ \  __ \  \ \ \-./\ \  \ \  __\   \ \ \/\ \ 
 \ \_\    \ \_\ \_\  \ \_\ \_\  \ \_\ \ \_\  \ \_____\  \ \____- 
  \/_/     \/_/ /_/   \/_/\/_/   \/_/  \/_/   \/_____/   \/____/ 
                                                                 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Payload
 * 
 * Payload Utility to grab data from $_REQUEST for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * PayloadPkg Class
 * 
 * @package FrameD
 * @subpackage core
 */
class PayloadPkg{

   /**
    * Package Data
    * 
    * @access private
    * @var mixed
    */
    private $data;
   
/**
 * PayloadPkg Construct
 * 
 * @param string $data Package data
 * @return void
 */
    function __construct($data){
	  $this->data = $data;
    }

/**
 * GetJSON
 * 
 * @access public
 * @return mixed
 */
    public function getJSON(){
	  return json_decode(stripslashes($this->data));
    }

/**
 * GetString
 * 
 * @access public
 * @return string
 */
    public function getString(){
	  return stripslashes($this->data);
    }

/**
 * GetInt
 * 
 * @access public
 * @return int
 */
    public function getInt($default=0){
	if($this->data+0){
	   return $this->data;
	} else {
	   return $default;
	}
    }

/**
 * GetFloat
 * 
 * @access public
 * @return float
 */
    public function getFloat($default=0){
	if($this->data+0){
	   return $this->data;
	} else {
	   return $default;
	}
    }

/**
 * GetHash
 * 
 * @access public
 * @return array
 */
    public function getHash($delimiter=','){
	return explode($delimiter, $this->data);
    }
}

/**
 * Payload Class
 * 
 * @package FrameD
 * @subpackage core
 */
class Payload {

/**
 * Payload Construct
 * 
 * @return void
 */
    function __construct(){
    $this->config = new Config();

	foreach($_POST as $index => $data){
	   $this->$index = new PayloadPkg($data);
	}
	foreach($_GET as $index => $data){
	   $this->$index = new PayloadPkg($data);
	}

    }

/**
 * GetParam
 * 
 * @access public
 * @param  string $str Variable name
 * @return object PayloadPkg 
 */
	public function getParam($str){
		if($this->$str){
		   return $this->$str;
	    } else {
		  return new PayloadPkg('');
		}
    }
}
?>
