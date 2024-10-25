<?php
require_once 'MyController.php';

class Uploadfiles extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'uploadfile';
        
        $this->check_session();
        // $this->check_permission();
	}

	public function index($id = 0)
	{
		
	}

	public function upload_image_course($data=null)
	{
		// print_r($_FILES);
		// print_r($_POST);
		// exit();
		
		if( count($_FILES) == 0 ){
			echo json_encode(array());
			return;
		}
		
		$tmp_folder = $_POST['id'];
		$tmp_folder = 'uploads/' . $tmp_folder . '/';

		$result = array();
		
		if( $_FILES["image"]["name"] != '' ){
			if(!is_dir($tmp_folder)) {
				mkdir($tmp_folder);
			}

			$f = $tmp_folder . 'image/';
			$this->deleteFolder( $f );
			
			$res = $this->save_file( $f , $_FILES['image']['name'], $_FILES["image"]["tmp_name"], $_FILES["image"]["size"]);
			$result['image'] = $res;
		}

		echo json_encode($result);
	}



	public function upload($data=null)
	{
		if( count($_FILES) == 0 ){
			echo json_encode(array());
			return;
		}

		// print_r($_FILES);
		// print_r($_POST);
		// exit();
		
		$tmp_folder = $_POST['course_id'] . '_' . $_POST['id'];
		$tmp_folder = 'uploads/' . $tmp_folder . '/';

		$result = array();
		
		if( $_FILES["video"]["name"] != '' ){
			if(!is_dir($tmp_folder)) {
				mkdir($tmp_folder);
			}

			$f = $tmp_folder . 'video/';
			$this->deleteFolder( $f );
			
			$res = $this->save_file( $f , $_FILES['video']['name'], $_FILES["video"]["tmp_name"], $_FILES["video"]["size"]);
			$result['video'] = $res;
		}

		if( $_FILES["pdf"]["name"] != '' ){
			if(!is_dir($tmp_folder)) {
				mkdir($tmp_folder);
			}
			
			$f = $tmp_folder . 'pdf/';
			$this->deleteFolder( $f );

			$res = $this->save_file( $f , $_FILES['pdf']['name'], $_FILES["pdf"]["tmp_name"], $_FILES["pdf"]["size"]);
			$result['pdf'] = $res;
		}

		echo json_encode($result);
	}
	private function save_file($folderPath, $filename, $tmp_name, $filesize){
		// File name

		if(!is_dir($folderPath)) {
			mkdir($folderPath);
		}

		// Maximum file size in bytes
		// $max_size = 2000000; // 2MB
		// if ($filesize > $max_size) {
   
		// 	  // Return response
		// 	  $response['status'] = 0;
		// 	  $response['msg'] = "The file exceeds the maximum file size of 2 MB.";
		// 	  return $response;
		// }

		// Upload file
		if (move_uploaded_file($tmp_name, $folderPath . $filename)) {

			// Return response
			$response['file_name'] = $filename;
			$response['path'] = $folderPath;
			return $response;

		}
	}

	function deleteFolder($folderPath) {
		// Check if the path exists and is a directory
		if (is_dir($folderPath)) {
			// Open the directory
			$contents = scandir($folderPath);
			foreach ($contents as $item) {
				if ($item != "." && $item != "..") {
					$itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;
					// If it's a file, delete it
					if (is_file($itemPath)) {
						unlink($itemPath);
					}
					// If it's a directory, recursively call the function
					elseif (is_dir($itemPath)) {
						$this->deleteFolder($itemPath);
					}
				}
			}
			// Delete the main directory
			rmdir($folderPath);
			
			return true;
		} else {
			// Return false if the path doesn't exist or is not a directory
			return false;
		}
	}
	function delete_all_file($arr){
		foreach( $arr as $k => $v ){
			$path = $v['path'];
			$this->deleteFolder($path);
		}
	}
	function delete_file(){
		$path = $_POST['path'];
		$file_name = $_POST['file_name'];

		unlink($path . $file_name);

		$this->RemoveEmptySubFolders($path);
		
		echo json_encode(array('success'=>true));
	}
	function RemoveEmptySubFolders($path)
	{
		if (is_dir($path)) {
			$empty=true;
			foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
			{
				$empty=false;
			}
			if ($empty) rmdir($path);
		}
	}
}