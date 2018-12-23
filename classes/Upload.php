<?php

namespace Reach\RImage;

use Reach\rImageFiles;
use Reach\RImage\Order;
use Reach\RImage\DatabaseHelper;

/**
 * This class handles the image upload.
 */
class Upload
{
    protected $id;
    protected $dir;

    /**
     * Class constructor.
     *
     * @param int $id The id of the K2 item.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->dir = (new rImageFiles($id))->getDir();
    }

    /**
     * Handle the image upload.
     *
     * @param array $file The array from the file upload
     *
     * @return null
     */
    public function handle($file)
    {
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
