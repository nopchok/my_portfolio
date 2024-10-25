<?php
require_once 'MyController.php';
require_once 'framework/models/Iso.php';

class AppIso extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'app/iso';
		
		$arr = array(
			'/iso_control/app/iso/get_cover',
			'/iso_control/app/iso/get_cover_iso',
			'/iso_control/app/iso/get_cover_header'
		);
		if( !in_array($_SERVER['REQUEST_URI'], $arr) ){
			$this->check_session();
			$this->check_permission();
		}
		
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'App > ISO';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/app/iso.php');
		$this->view->render('framework/views/template/footer.php');
	}
	
	public function get_project()
	{
        $md = new MdIso();
        $res = $md->get_project($this->post_data);
		echo json_encode($res);
    }

	public function get_document_aadata()
	{
        $md = new MdIso();
        $res = $md->get_document_aadata($this->post_data);
		echo json_encode($res);
    }

	public function save_document()
	{
        $md = new MdIso();
        $action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}
	public function get_document()
	{
        $md = new MdIso();
        $res = $md->get_document($this->post_data);
		echo json_encode($res);
	}
	public function get_iso_dwg()
	{
        $md = new MdIso();
        $res = $md->get_iso_dwg($this->post_data);
		echo json_encode($res);
	}
	public function get_cover_iso()
	{
		
		// fetch('http://127.0.0.1/iso_control/app/iso/get_cover_iso', {
		// 	body: JSON.stringify( {document_id: 2, iso_lot_id: 2 } ),
		// 	method: "POST"
		// }).then(j=>j.json()).then(console.log)

		$this->post_data = json_decode(file_get_contents("php://input"), true);
        $md = new MdIso();
        $res = $md->get_iso_dwg_issue($this->post_data);
		echo json_encode($res);
	}
	public function get_cover_header(){
		$this->post_data = json_decode(file_get_contents("php://input"), true);
        $md = new MdIso();
        $res = $md->get_cover_header($this->post_data);
		echo json_encode($res);
	}
	public function get_cover(){
		$this->post_data = json_decode(file_get_contents("php://input"), true);
        $md = new MdIso();
        $action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}



	public function get_iso_dwg_issue()
	{
        $md = new MdIso();
        $res = $md->get_iso_dwg_issue($this->post_data);
		echo json_encode($res);
	}
	
	public function get_iso_lot()
	{
        $md = new MdIso();
        $res = $md->get_iso_lot($this->post_data);
		echo json_encode($res);
	}
	
	public function save_iso_dwg()
	{
		$this->post_data = json_decode(file_get_contents("php://input"), true);
		$md = new MdIso();
		$action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}
	public function save_iso_lot()
	{
		$md = new MdIso();
		$action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}


	
	public function get_iso_revision()
	{
        $md = new MdIso();
        $res = $md->get_iso_revision($this->post_data);
		echo json_encode($res);
	}
	public function save_iso_revision()
	{
		$this->post_data = json_decode(file_get_contents("php://input"), true);
        $md = new MdIso();
        $res = $md->save_iso_revision($this->post_data);
		echo json_encode($res);
	}
}