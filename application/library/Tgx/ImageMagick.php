<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */

class Tgx_ImageMagick
{
	private $_fileName;
	private $_execIdentify = '/usr/bin/identify';
	private $_execConvert = '/usr/bin/convert';
	private $_identify;
	public $output = '';
	
	public function __construct ($fileName)
	{
		$this->output .= $fileName."\n";
		$this->_fileName = $fileName;
	}
	
	public function identify ()
	{
		$this->output .= "Tgx_ImageMagick->identify \n";
		$return = array ();
		$exec = $this->_execIdentify.' '.$this->_fileName;
		if ($lines = $this->run ($exec)) {
			$this->output .= "Identify returned ".count($lines)."\n";
			foreach ($lines as $line) {
				$this->output .= $line."\n";
				$aDetails = explode(" ", substr ($line,strlen(dirname($this->_fileName.'/'))));
				$return[] = $aDetails;
//				$aDimentions = explode ("x", $aDetails[2]);
//				//var_dump($aDimentions);
//				if ($aDimentions[0]>2500 || $aDimentions[1]>2500)
//					$errors[] = "Image dimensions are too large (".$aDimentions[0]."px by ".$aDimentions[1]."px). <br />Please upload an image with dimensions of 2500px by 2500px or less";
//					
				
			}
			$this->_identify = $return;
			return $return;
		} else {
			$this->output .= "Could not identify file\n";
		}
		return false;
	}
	
	function convert ($page, $outputFilename = '')
	{
		$this->output .= "Tgx_ImageMagick->convert \n";
		// convert
		$path_parts = pathinfo ($this->_fileName);
		if (empty($outputFilename))
			$outputFilename = $path_parts['dirname'].'/'.$path_parts['filename'].'_'.$page.'.png';
		$inputFilename = $this->_fileName.'['.$page.']';
		$exec = $this->_execConvert." $inputFilename -strip -flatten -thumbnail 800x800> -quality 85 $outputFilename";
		
		$this->output .= $exec." \n";
		if ($lines = $this->run($exec))
		{
			$this->output .= "convert succeeded \n";
			return $outputFilename;
		}else {
			$this->output .= "convert failed \n";
			return false;
		}
	}
	
	function run ($command) 
	{		
		$descriptorspec = array(
		   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		   2 => array("pipe", "w") // stderr is a file to write to
		);
		
		$cwd = '/tmp';
		$env = array('some_option' => 'aeiou');
		
		$process = @proc_open($command, $descriptorspec, $pipes, $cwd, $env);
		
		if (is_resource($process)) {
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			// Any error output will be appended to /tmp/error-output.txt
		
			$return = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
					   
			// Read StdErr
			$stdErr = '';
			while(!feof($pipes[2]))    {
				$stdErr .= fgets($pipes[2], 1024);
			}
			fclose($pipes[2]);
		
			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$return_value = proc_close($process);
			
//			echo nl2br($return);
			$lines = explode("\n", $return);
			array_pop ($lines);
			
//			echo "command returned $return_value\n";
			$this->output .= $stdErr;
			
			if (count($lines)>0)
				return $lines;
			else if ($return_value ==0)
 				return true;
			else
				return false; 
		}
		return false;
	}
	
}
