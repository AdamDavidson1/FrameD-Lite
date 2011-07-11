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
 * Logger
 * 
 * Simple Logger for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * Logger Class
 * 
 * @package FrameD
 * @subpackage core
 */
class Logger {

/**
 * Logger Construct
 * 
 * @access public
 * @param string $class  Name of class or file for logger
 * @return void
 */
   function __construct($class='SYSTEM'){
	   $this->config = new Config();
       $this->class = $class;
       $this->level = $this->config->environment['LOG']['level'];
   }

/**
 * Logger Debug
 * 
 * @access public
 * @param string $message  Message to log for debugging
 * @return void
 */
   public function debug($message){
	if($this->level <= 1){
	   $this->message('DEBUG-- '.$this->class.' : '.$message);
	}
	   return;
   }
/**
 * Logger Trace
 * 
 * @access public
 * @param string $message  Message to log for tracing the system's process
 * @return void
 */
   public function trace($message){
	if($this->level <= 3){
       $this->message('TRACE-- '.$this->class.' : '.$message);
    }
       return;

   }
/**
 * Logger Error
 * 
 * @access public
 * @param string $message  Message to log for system or db errors
 * @return void
 */
   public function error($message){
	if($this->level <= 3){
       $this->message('ERROR-- '.$this->class.' : '.$message);
    }
       return;

   }
/**
 * Message
 * 
 * Writes the message to the log file
 *
 * @access private
 * @param string $message  Message to log for system or db errors
 * @return void
 */
   private function message($message){

	$file = fopen($this->config->environment['LOG']['file'],'a');

	fwrite($file, date('Y-m-d H:i:s', time()).' -- '.$message."\n\n");

	fclose($file);

	return;
   }

}
?>
