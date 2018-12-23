<?php

namespace Reach\RImage;

use Reach\rImageFiles;

/**
 * The class that handles the JSON file with the images order.
 */
class Order
{
    protected $id;
    protected $dir;

    /**
     * Class contructor
     *
     * @param int $id The id of the K2 item.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->dir = (new rImageFiles($id))->getDir();
    }

    /**
     * Gets the array of the images order from the json file.
     *
     * @return array|bool Returns the array or false on error
     */
    protected function getOrderArray()
    {
        $path = $this->dir.'/order.json';
        if (file_exists($path)) {
            return json_decode(file_get_contents($path));
        }
        return false;
    }

    /**
     * Removes an image from the order array.
     * 
     * @param string $file Image's path.
     * 
     * @return null|bool Returns null if the array is empty or true if it's saved. 
     */
    public function removeFromOrderArray($file)
    {
        $array = $this->getOrderArray();
        if (! $array) {
            return;
        }
        if (($key = array_search($file, $array)) !== false) {
            array_splice($array, $key, 1);
            return $this->saveOrderJson($array);
        }
    }

    /**
     * Add image to the orderarray.
     * 
     * @param string $file Path to the image file.
     *
     * @return null|bool Returns null if the array is empty or true if it's saved. 
     */
    public function addToOrderArray($file)
    {
        $array = $this->getOrderArray();
        if (! $array) {
            return;
        }
        $path = str_replace(JPATH_ROOT, '', $file);
        $array[] = $path;
        return $this->saveOrderJson($array);
    }

    /**
     * Save the order at the JSON file.
     * 
     * @param array $order The array to be saved.
     *
     * @throws Exception Throws an exception if it can't save.
     * 
     * @return bool Returns true at save.
     */
    public function saveOrderJson($order)
    {
        $path = $this->dir.'/order.json';
        $json_data = json_encode($order);

        if (file_exists($path)) {
            unlink($path);
        }

        if (!file_put_contents($path, $json_data)) {
            throw new Exception('Cannot write file.');
        } else {
            return true;
        }
    }

    /**
     * Delete the order JSON file.
     * 
     * @return null
     */
    public function removeOrderJson()
    {
        $path = $this->dir.'/order.json';
        unlink($path);
    }
}
