<?php

namespace Reach\RImage;

use Reach\rImageFiles;

// The class that handles the JSON file with the images order
class Order {

    protected $id;
    protected $dir;

    function __construct($id) {
        $this->id = $id;
        $this->dir = (new rImageFiles($id))->getDir();
    }

    protected function getOrderArray() {
        $path = $this->dir.'/order.json';
        if (file_exists($path)) {
            return json_decode(file_get_contents($path));
        }
        return false;
    }

    public function removeFromOrderArray($file) {
        $array = $this->getOrderArray();
        if (! $array) {
            return;
        }
        if (($key = array_search($file, $array)) !== false) {
            array_splice($array, $key, 1);
            return $this->saveOrderJson($array);
        }
    }

    public function addToOrderArray($file) {
        $array = $this->getOrderArray();
        if (! $array) {
            return;
        }
        $path = str_replace(JPATH_ROOT, '', $file);
        $array[] = $path;
        return $this->saveOrderJson($array);
    }

    public function saveOrderJson($order) {
        $path = $this->dir.'/order.json';
        $json_data = json_encode($order);

        if (file_exists($path)) {
            unlink($path);
        }

        if (!file_put_contents($path, $json_data)) {
            throw new Exception('Cannot write file.');
        }
        else {
            return true;
        }
    }

    public function removeOrderJson() {
        $path = $this->dir.'/order.json';
        unlink($path);
    }

}
