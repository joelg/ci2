<?php

class MY_Controller extends Controller  {

    function __construct()  {
        parent::__construct();
		
		// set up notices and errors in view
		$notices = $this->session->flashdata('notices');
		$this->view->set('notices', $notices);
		$global_errors = $this->session->flashdata('global_errors');
		$this->view->set('global_errors', $global_errors);
		
		// set up user information for the view
		$this->view->set('email', $this->session->userdata('email'));
		
		// enable the profile if this is dev environment
		if(!LIVE && !IS_AJAX) $this->output->enable_profiler(true);
    }

	function _header()
	{
		$title = $this->view->get('title');
		if(empty($title)) $this->view->set('title', 'Your title');
		$this->view->load('common/header');
	}
	function _footer()
	{
		$this->view->load('common/footer');
	}

	function _json( $data, $encode = true ) {
		header('Content-Type: application/json;charset=UTF-8');

		if ( $encode ) {
			echo json_encode( $data );
		}
		else {
			echo $data;
		}
	}
}