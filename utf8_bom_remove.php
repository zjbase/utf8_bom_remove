<?php
if (isset($_GET['dir'])){ //设置文件目录
$basedir=$_GET['dir'];
}else{
$basedir = '.';
}
$counter = 0 ;  
if(isset($_GET['w']) && $_GET['w']==1)
{
	checkdir($basedir,true);
}else{
	checkdir($basedir);
}



function checkdir($basedir,$erasebom=false){

 	global $counter;
	if ($dh = opendir($basedir)) {
		while (($file = readdir($dh)) !== false) {
			if ($file != '.' && $file != '..'){
				 
				if (!is_dir($basedir.DIRECTORY_SEPARATOR.$file)) {
					 
					$checkbom_r = checkBOM($basedir.DIRECTORY_SEPARATOR . $file);
					if($checkbom_r==0) {
						$counter++;
						if($erasebom){
 							rewrite ($basedir. DIRECTORY_SEPARATOR .$file);
							echo $basedir . DIRECTORY_SEPARATOR . $file  . " bom found , erase bom ok <br/> " . PHP_EOL;
						}else{
							echo $basedir . DIRECTORY_SEPARATOR . $file  . " bom found <br/> ". PHP_EOL ;	
						}
					}
					
				}else{
					$dirname = $basedir. DIRECTORY_SEPARATOR .$file;
					checkdir($dirname,$erasebom);
				}
			}
		}
	closedir($dh);
	}
}

echo "<br/> all done , total counter is " . $counter;
function checkBOM ($filename) {
	if(substr($filename,-3)=='php'){
		$contents = file_get_contents($filename);
		$charset[1] = substr($contents, 0, 1);
		$charset[2] = substr($contents, 1, 1);
		$charset[3] = substr($contents, 2, 1);
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
			return 0;
		}else{
			return 2;
		}
	}else{
		return 1;
	}
}
function rewrite ($filename) {
    $data = substr(file_get_contents($filename),3);
    $filenum = fopen($filename, "w");
    flock($filenum, LOCK_EX);
    fwrite($filenum, $data);
    fclose($filenum);
}
