<?php

require_once("../../../../../wp-load.php");

global $userpro;

// Secure file uploads
if( isset($_FILES["userpro_file"]) ) {
	if (empty($_FILES["userpro_file"]["name"])){
		die();
	} else {
		if ($_FILES["userpro_file"]["error"] > 0){
			die();
		} else {
			if(!is_uploaded_file($_FILES["userpro_file"]["tmp_name"])){
				die();
			} elseif( $_FILES["userpro_file"]["size"]>8388608 ){
				die();
			} else {
                $file_extension = strtolower(strrchr($_FILES["userpro_file"]["name"], "."));
                if( !in_array($file_extension, array( '.gif','.jpg','.png','.pdf','.txt','.zip','.doc','.jpeg'  )  ) ){
					die();
                }else{
					if(!is_array($_FILES["userpro_file"]["name"])) {
						$unique_id = uniqid();
						$ret = array();
						$target_file = $userpro->get_uploads_dir() . $unique_id . $file_extension;
						move_uploaded_file( $_FILES["userpro_file"]["tmp_name"], $target_file );
						$ret['target_file'] = $target_file;
						$ret['target_file_uri'] = $userpro->get_uploads_url() . basename($target_file);
						echo json_encode($ret);
					}
				}
			}
		}
	}
}