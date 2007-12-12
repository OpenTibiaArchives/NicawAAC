<?
/*
EXAMPLE SCRIPT
Shows random picture in folder
*/
function get_extension ($filename)
{
	$filename = strtolower($filename) ;
	$ext = split("[/\\.]", $filename) ;
	$n = count($ext)-1;
	$ext = $ext[$n];
	return $ext;
} 
$i = 0;
$extensions = array('jpg','gif','png');
if ($dir = opendir('.')){
	while (false !== ($f = readdir($dir))) {
		if (in_array(get_extension($f), $extensions)){
			$files[$i++] = $f;
		}
	}
}
if (isset($files)){
	$file = $files[rand(0,$i-1)];
	$size = getimagesize($file);
	if ($size === false) die();
	//correct aspect ratio
	if ($size[0] > $size[1]){
		$width = 200;
		$height = $size[1]/$size[0]*$width;
	}else{
		$height = 200;
		$width = $size[1]/$size[0]*$height;
	}
	echo '<a href="gadgets/Random Picture/'.$file.'"><img width="'.$width.'px" height="'.$height.'px" alt="POTD" src="gadgets/Random Picture/'.$file.'"/></a>';
}
?>