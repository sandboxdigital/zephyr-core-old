<?php

class Tgx_Ffmpeg
{
	
	public static $output;
	
		/*
	 * // get video width and height - 10-14-07
// for testing call with: ffmpeg_get_width_height.php?file=video.flv

$videofile = (isset($_GET['file'])) ? strval($_GET['file']) : 'video.flv';

ob_start();
passthru("ffmpeg-10141.exe -i \"". $videofile . "\" 2>&1");
$size = ob_get_contents();
ob_end_clean();

preg_match('/(\d{2,4})x(\d{2,4})/', $size, $matches);
$width = $matches[1];
$height = $matches[2];

print "  Size: " . $size   . "<br />\n";
print " Width: " . $width  . "<br />\n";
print "Height: " . $height . "<br />\n";

?>
	 
	private static function _ffmpeg_exec($cmd)
	{
		self::$output = 'start'; 
		self::$output .= '1' . "\n";
		$handle = popen($cmd, 'w');
		self::$output .= "'$handle'; " . gettype($handle) . "\n";
		$read = fread($handle, 2096);
		self::$output .= $read;
		pclose($handle);
		
		return ;
	}//fn ffmpeg_exec
*/
	
	
	private static function _ffmpeg_proc($cmd)
	{
		self::$output = 'start'."\n";
		$descriptorspec = array(
		   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		   2 => array("pipe", "a") // stderr is a file to write to
		);
		
		$cwd = '/tmp';
		$env = array('some_option' => 'aeiou');
		
		self::$output = $cmd."\n"; 
		$process = proc_open($cmd, $descriptorspec, $pipes, null, null);
		
		if (is_resource($process)) {		
//		    fwrite($pipes[0], '<?php print_r($_ENV); ?'.'>');
//		    fclose($pipes[0]);
		
		    echo stream_get_contents($pipes[1]);
		    fclose($pipes[1]);
					    
//		    $stderr = '';
//		    while(!feof($pipes[2])) { 
//		    	$stderr .= fgets($pipes[2], 128); 
//		    }
//		    echo $stderr;
//		    fclose($pipes[2]);
		    
		    echo stream_get_contents($pipes[2]);
		    fclose($pipes[2]);
		
		    // It is important that you close any pipes before calling
		    // proc_close in order to avoid a deadlock
		    $return_value = proc_close($process);
		
		    self::$output .= "command returned $return_value\n";
		    
		    return $return_value;
		}
	}
	
	public static function convertToFLV($inVideo, $outVideo, $options = array())
	{
		
		
		
		/*
		 * -sameq means keep the same quality
-acodec libmp3lame means use the lame MP3 library for the audio codec
-ar 22050 means use an audio sampling rate of 22050Hz
-ab 96000 means use an audio bit rate of 96000bps
-deinterlace means deinterlace the video
-nr 500 means use noise reduction of 500
-s 320x240 means make a video that is 320 pixels wide and 240 pixels high
-aspect 4:3 means maintain an aspect ratio (width:height) of 4:3
-r 20 means maintain a frame rate of 20fps
-g 500 means use a group of pictures of 500 (keyframes every 500 frames or 25 seconds at 20fps)
-me_range 20 means limit motion vectors range to 20
-b 270k means use a video bitrate of 270000bps
-f flv means output into an FLV container file
-y means overwrite if the ouput file already exists
		 */
		$frameRate = 20;
		$keyFrameRate = $frameRate * 5;
		$arg = "ffmpeg -i $inVideo  -acodec libmp3lame -ar 22050 -ab 96000 -sameq -nr 500 -s 720x406 -r $frameRate -g $keyFrameRate -me_range 20 -b 300k -f flv -y $outVideo";
		$exec = self::_ffmpeg_proc($arg);
		
//		echo $outVideo;
//		echo '<br />';
//		echo file_exists($outVideo);
		if(file_exists($outVideo))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}