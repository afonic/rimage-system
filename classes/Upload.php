<?php

namespace Reach\RImage;

use Reach\rImageFiles;
use Reach\RImage\Order;
use Reach\RImage\DatabaseHelper;
use Joomla\CMS\Helper\MediaHelper;

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
        // Make sure filename is safe
        $safeFileName = \JFile::makeSafe($file['name']);
        // Abort if the file is not an image
        if (! MediaHelper::isImage($safeFileName)) {
            throw new \Exception("Not an image file extension!");
        }
        if (! getimagesize($file['tmp_name'])) {
            throw new \Exception("Not a real image file!");
        }
        // Make the dir if needed
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }
        move_uploaded_file($file['tmp_name'], $this->dir.$safeFileName);
        $db = new DatabaseHelper($this->id);
        $db->addGalleryColumn();
        $order = new Order($this->id);
        $order->addToOrderArray($this->dir.$safeFileName);
    }
}
