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
 * Core
 * 
 * Simple requires for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */

date_default_timezone_set('America/Los_Angeles');

ini_set('display_errors',false);
ini_set('log_errors',1);
ini_set('error_log', 'logs/phperrors.log');

set_include_path('core/lib/pear/php/:../core/lib/pear/php/:../:./');

/**
 * FrameD Config
 */
require_once('core/config.php');

/**
 * FrameD Logger
 */
require_once('core/logger.php');

/**
 * FrameD Payload
 */
require_once('core/payload.php');

/**
 * FrameD SessionData
 */
require_once('core/sessiondata.php');

/**
 * FrameD MySQLDb
 */
require_once('core/databases/MySQLDb.php');

/**
 * FrameD SQLite3Db
 */
require_once('core/databases/SQLite3Db.php');

 /**
  * Get File Name
  *
  * @ignore
  */
function get_current(){
	$path = explode('/',$_SERVER['SCRIPT_FILENAME']);
	return $path[(count($path)-1)];
}

class Controller{

    private $data;

    private $format;

    private $metadata;

	function __construct($format, $sessionData){
		$this->format = $format;
		$this->sessionData = $sessionData;
		$this->logger = new Logger('Controller');
    }

/**
 * Add View Meta
 * 
 * @access public
 * @param string $key Name of Data to be used when Rendering
 * @param string $data Data to be used when Rendering
 * @return void
 */
	public function addViewMeta($key, $data){
		$this->metadata[$key] = $data;

		return;
    }


	public function render($view){
		$data = $this->data;

		if($this->metadata){
				foreach($this->metadata as $key => $value){
					${$key} = $value;
				}
		}

		header('Set-Cookie: '.
		 $this->config->environment['SESSIONDATA']['cookie']['name'].'='.
		 urlencode($this->sessionData->getEncrypted()).'; HttpOnly'
		 );


		if($this->format == '' || strtolower($this->format) == 'html' || strtolower($this->format) == 'php' && $view != NULL){
		   if(is_file('views/html/'.$view.'.php')){

			   require('views/html/'.$view.'.php');

		   } elseif(is_file('core/views/html/'.$view.'.php')){

			   require('core/views/html/'.$view.'.php');

		   }
		} else {
			if(is_file('views/'.$this->format.'/'.$view.'.php')){

			    require('views/'.$this->format.'/'.$view.'.php');

			} elseif(is_file('core/views/'.$this->format.'/'.$view.'.php')){

				require('core/views/'.$this->format.'/'.$view.'.php');

			} else {

			  $this->renderFormat($data);

			}
		}

		return;
	}

	public function setViewData($data){
		$this->data = $data;

		return;
    }

	private function renderFormat($data){
		switch(strtolower($this->format)){
			case 'json':
			case 'js':
				header('Content-Type: application/json');
				echo $this->writeJSON($data);
			break;
			case 'data':
				echo "<pre>Data:\n\n";
				print_r($data);
				echo '</pre>';
			break;
			case 'text':
				if(is_array($data)){
					print_r($data);
				} else {
					echo $data;
				}
			break;

			case 'xml':
				header('Content-Type: text/xml');
				echo $this->writeXML($data);

			break;
		}
	}

/**
 * Write JSON
 * 
 * Writes data as JSON
 *
 * @access private
 * @param  string  $data Package data
 * @return string  JSON
 */
    private function writeJSON($data){
		if(!class_exists('XML_Serializer')){
                require_once('JSON.php');
        }
		$json = new Services_JSON();

		return $json->encode($data);
    }



/**
 * Write XML
 * 
 * Writes data as XML
 *
 * @access private
 * @param  string  $array Package data
 * @return string  XML
 */
	private function writeXML($array){
		if(!class_exists('XML_Serializer')){
				require_once('XML/Serializer.php');
		}

			$options = array(
				XML_SERIALIZER_OPTION_INDENT      => "\t",     // indent with tabs
				XML_SERIALIZER_OPTION_RETURN_RESULT => true,
				XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",     // use UNIX line breaks
				XML_SERIALIZER_OPTION_ROOT_NAME   => 'data',// root tag
				XML_SERIALIZER_OPTION_DEFAULT_TAG => 'item'    // tag for values 
															   // with numeric keys
		   );
		 
			$serializer = new XML_Serializer($options);
    		return '<?xml version="1.0" encoding="UTF-8"?>'."\n".$serializer->serialize($array);
	}
}

$logger      = new Logger(get_current());
$payload     = new Payload();
$sessionData = new SessionData();

$format = $payload->getParam('format')->getString();

$controller = new Controller($format, $sessionData);


?>
