<?php

namespace Reach\RImage;

use Reach\rImageFiles;
use Reach\RImage\Order;
use Reach\RImage\DatabaseHelper;

class Delete
{
    protected $id;
    
    /**
     * Class contructor
     * 
     * @param int $id The id of the K2 item
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    /**
     * Handle the deletion
     * 
     * @param string $file The path to the file
     * 
     * @return bool
     */
    public function handle($file)
    {
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
        return true;
    }
}
