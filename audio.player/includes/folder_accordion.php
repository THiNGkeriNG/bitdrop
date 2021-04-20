<?php

	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	if($_SERVER['HTTP_HOST'] == "localhost"){
		define('SITE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/');
		define('SITEPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
	}
	else{ 
		define('SITE_URL', $protocol . $_SERVER['HTTP_HOST']);
		define('SITEPATH', $_SERVER['DOCUMENT_ROOT']);
	}
	
	$dir = $_REQUEST['dir'];
	
	if(file_exists($dir)==false){
		echo 'Directory \'', $dir, '\' not found!';
	}else if( !is_readable($dir) ) {
		echo 'Directory \'', $dir, '\' is not readable! Check your permissions!';
	}else{
	
		$media = array();

		$di = new DirectoryIterator($dir);
		foreach ($di as $fileinfo) {
		    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
		        $media[] = array(
		        	'parent' => $fileinfo->getFilename(),
		        	'children' => getDirContent($fileinfo->getRealpath())
		        );
		    }
		}

		echo json_encode($media);
	}

	function getDirContent($dir){

		$type = array('mp3','wav','aac','flac');

		$media = array();

		$di = new DirectoryIterator($dir);
		foreach ($di as $fileinfo) {
			$path_info = pathinfo($fileinfo->getPathname());
			if(isset($path_info['extension'])){
				if(in_array(strtolower($path_info['extension']), $type)){

					$media[] = array( 
						"SITE_URL" => SITE_URL, 
						"SITEPATH" => SITEPATH, 
						"fullpath" => SITE_URL.'/'.path2url(realpath($path_info['dirname'])).'/'.$path_info['basename'],  
						"basename" => $path_info['basename'], 
						"extension" => $path_info['extension'],
						"dirname" => realpath($path_info['dirname']),
						"filename" => $path_info['filename'],
						"filemtime" => filemtime($path_info['dirname'].'/'.$path_info['basename'])
					); 

				}
			}
			
		}

		return $media;
	}

	function path2url($dirname) {
		return str_replace(SITEPATH, '', str_replace('\\', '/', $dirname));
	}
?>
