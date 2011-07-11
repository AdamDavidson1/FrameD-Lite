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

require_once('Crypt/Blowfish.php');



/**
 * Session Data
 * 
 * Session data from cookie
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * SessionDataPkg Class
 * 
 * @package FrameD
 * @subpackage core
 */
class SessionDataPkg {

   /**
    * Package Data
    * 
    * @access private
    * @var mixed
    */
    private $data;

   /**
    * Package Name
    * 
    * @access private
    * @var mixed
    */
    private $name;

   
/**
 * SessionDataPkg Construct
 * 
 * @param string $name Package name
 * @param string $data Package data
 * @return void
 */
    function __construct($name, $data){
	  $this->data = $data;
	  $this->name = $name;
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

/**
 * GetData
 * 
 * @access public
 * @return array
 */
    public function getData(){
		return $this->data;
	}

/**
 * GetName
 * 
 * @access public
 * @return array
 */
    public function getName(){
		return $this->name;
	}
}


/**
 * SessionData Class
 * 
 * @package FrameD
 * @subpackage core
 */
class SessionData {

   /**
    * Package Data
    * 
    * @access private
    * @var mixed
    */
     private $package;

/**
 * SessionData Construct
 * 
 * @return void
 */
   function __construct(){
	   $this->logger = new Logger('SessionData');

	   $this->config = new Config();

	   $config = $this->config->environment['SESSIONDATA']['cookie'];

	   if(!$config){
		  $this->logger->trace('SessionData Not Loaded');
		  return;
	   }

	   if(!$this->cookie){
		  $this->cookie = $_COOKIE[$config['name']];
	   }

	   if($this->cookie){

		  $sessionData = $this->deserialize($this->decrypt(base64_decode(($this->cookie))));

		  if($sessionData){
				  foreach($sessionData as $index => $data){
					 $this->load($index, $data);
				  }
		  }
	   }
	   
   }


/**
 * Load
 * 
 * Makes new SessionDataPkg
 *
 * @access private
 * @param  string $name    Name of the new element
 * @param  string $element Data of the new element
 * @return void
 */
   private function load($name, $element){
	  $this->package->$name = new SessionDataPkg($name, $element);

	  return;
   }

/**
 * Get Pkg 
 * 
 * Returns requested SessionDataPkg
 *
 * @access public
 * @param  string $name  Name of the item in SessionData
 * @return SessionDataPkg 
 */
   public function getPkg($name){
	  if($this->package->$name){
	     return $this->package->$name;
	  } else {
		 return new SessionDataPkg($name,null);
	  }
   }

/**
 * Set Pkg 
 * 
 * Returns requested SessionDataPkg
 *
 * @access private
 * @param  string $name  Name of the item in SessionData
 * @return SessionDataPkg 
 */
   public function setPkg($name, $value){
	  return $this->load($name, $value);
   }

/**
 * Decrypt
 * 
 * Decrypts string given
 *
 * @access private
 * @param  string $data String to be decrypted
 * @return string clear text
 */
   private function decrypt($data){

	  $bf =& Crypt_Blowfish::factory('cbc');

	  $iv = 'abc123+=';
	  $key = $this->config->environment['SESSIONDATA']['cookie']['key'];

	  $bf->setKey($key, $iv);

	  $cleartext = $bf->decrypt($data);

	  if(extension_loaded('zlib') && $this->config->environment['SESSIONDATA']['cookie']['compress']){
		$data = gzuncompress($data);
	  }

	  return $cleartext;
   }

/**
 * Encrypt
 * 
 * Encrypts string
 *
 * @access private
 * @param  string $data Data to be encrypted
 * @return string encrypted
 */
   private function encrypt($data){

		 $bf =& Crypt_Blowfish::factory('cbc');

		 $iv = 'abc123+=';

		 $key = $this->config->environment['SESSIONDATA']['cookie']['key'];

		 $bf->setKey($key, $iv);

		 $crypttext = $bf->encrypt($data);

	  if(extension_loaded('zlib') && $this->config->environment['SESSIONDATA']['cookie']['compress']){
		$crypttext = gzcompress($crypttext, $this->config->environment['SESSIONDATA']['cookie']['compress_level'] ? 
											$this->config->environment['SESSIONDATA']['cookie']['compress_level'] : 1);
	  }

	  return $crypttext;
   }

/**
 * Serialize
 * 
 * Combines all data into string
 *
 * @access private
 * @param  mixed  $data Data to be serialized
 * @return string json encoded string
 */
   private function serialize($data){
	  return json_encode($data);
   }

/**
 * Deserialize
 * 
 * Takes string and converts it into Data
 *
 * @access private
 * @param  string  $data String to be transformed into Data
 * @return mixed   data
 */
   private function deserialize($data){
	  return json_decode(trim($data));
   }

/**
 * Get Encrypted
 * 
 * Combines all Session Data, encrypts it, and returns it
 *
 * @access public
 * @return string encrypted for storage
 */
   public function getEncrypted(){
	  if(!$this->package){ return; }
	  foreach($this->package as $package){
		 $hash[$package->getName()] = $package->getData();
	  }

	  $crypt_session = (base64_encode($this->encrypt($this->serialize($hash))));

	  return $crypt_session;
   }
}
?>
