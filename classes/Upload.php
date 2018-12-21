<?php

namespace Reach\RImage;

use Reach\rImageFiles;
use Reach\RImage\Order;
use Reach\RImage\DatabaseHelper;

// This handles an image upload
class Upload {

    protected $id;
    protected $dir;

    function __construct($id) {
        $this->id = $id;
        $this->dir = (new rImageFiles($id))->getDir();
    }

    public function handle($file) {
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }
        move_uploaded_file($file['tmp_name'], $this->dir.$file['name']);
        $db = new DatabaseHelper($this->id);
        $db->addGalleryColumn();
        $order = new Order($this->id);
        $order->addToOrderArray($this->dir.$file['name']);
    }


}