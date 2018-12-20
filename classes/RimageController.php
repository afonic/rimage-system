<?php

namespace Reach;

use Reach\Order;
use Reach\Upload;
use Reach\Delete;
use Reach\rImageForceRegeneration;

class RimageController {

	protected $app;
	protected $task;
	protected $input;
	protected $user;
	protected $validTasks;

	function __construct($app, $user) {
		$this->app = $app;
		$this->task = $app->input->get('rimage');
		$this->input = $app->input;
		$this->user = $user;
		$this->validTasks = ['upload', 'order', 'regenitem', 'regen', 'delete'];
		$this->execute();
	}

	protected function execute() {		
		if (in_array($this->task, $this->validTasks)) {
			// Check if we are in the admin panel
			$this->checkIfInAdmin();
			// Authorize user
			$this->authorize();
			// Send it to the appropriate action
			$this->{$this->task}();
		}		
	}
	
	// Handle changing the order
	protected function order() {
		$this->checkToken();
	   	$id = $this->input->get('rid');
        $array = $this->input->get('rdata', null, RAW);
        if ($id && $array) {
	        try
	        {
	            $order = new Order($id);
	            $order->saveOrderJson($array);
	            echo new \JResponseJson();
	            jexit();
	        }
	        catch (\Exception $e)
	        {
	            header("HTTP/1.0 500 Error");
	            echo new \JResponseJson($e);
	            jexit();
	        }
        }
	}
	
	// Handle single item regen
	protected function regenitem() {
		$this->checkToken();
	    $id = $this->input->get('rid');
        $catid = $this->input->get('rcatid');
        $gallery = $this->input->get('rgallery');
        if ($id && $catid) {
            $regenerator = new rImageForceRegeneration;
            $regenerator->regenerateSingle($id, $catid, $gallery);
            echo new \JResponseJson();
            jexit();
        }
	}
	
	// Handle general regen
	protected function regen() {
		$regenerator = new rImageForceRegeneration;
        $regenerator->regenerate();
	}
	
	// Handle file deletion
	protected function delete() {
	    $this->checkToken();
        $id = $this->input->get('rid');
        $file = $this->input->get('rfile', null, RAW);
        if ($id && $file) {
	        try
	        {
				$delete = new Delete($id);
				$delete->handle($file);
	            echo new \JResponseJson();
	            jexit();
	        }
	        catch (Exception $e)
	        {
	            header("HTTP/1.0 500 Error");
	            echo new \JResponseJson($e);
	            jexit();
	        }
        }
	}
	
	// Handle upload
	protected function upload() {
		$this->checkToken(true);
	    $id = $this->input->get('rid');
        $file = $this->input->files->get('file');
		    if ($id && $file) {
		    try
		    {
		        $upload = new Upload($id);
		        $upload->handle($file);
		        echo new \JResponseJson();
		        jexit();
		    }
		    catch (Exception $e)
		    {
		        header("HTTP/1.0 500 Error");
		        echo new \JResponseJson($e);
		        jexit();
        	}
        }
	}
	
	// Authorize the user
	protected function authorize() {
		if (! $this->user->authorise('core.create', 'com_k2')) {
			jexit('Not authorized');
		}
	}
	
	// Check if the form token exists
	protected function checkToken($get = null) {
		if ($get) {
			if (! \JRequest::checkToken('get')) {
				var_dump('got here');
				jexit('Invalid token');
			}
		} else {
			if (! \JRequest::checkToken()) {
				jexit('Invalid token');
			}
		}
	}
	
	// Make sure we are in the admin panel
	protected function checkIfInAdmin() {
		if (! $this->app->isAdmin()) {
			jexit('Only works in admin!');
		}
	}

}