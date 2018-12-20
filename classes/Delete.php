<?php

namespace Reach\RImage;

use Reach\rImageFiles;
use Reach\RImage\Order;
use Reach\RImage\DatabaseHelper;

// This handles an image upload
class Delete {

	protected $id;

	function __construct($id) {
		$this->id = $id;
	}

	public function handle($file) {
		// Delete the file
	    unlink(JPATH_ROOT.$file);
        $order = new Order($this->id);
        $order->removeFromOrderArray($file);
        // If there are no files left, delete DB column and order Json
        if (! (new rImageFiles($this->id))->hasGallery()) {
			$db = new DatabaseHelper($this->id);
			$db->deleteGalleryColumn();
			$order->removeOrderJson();
        }
	}

}