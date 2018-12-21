<?php

namespace Reach\RImage;

// Helper functions for database calls
class DatabaseHelper {

    protected $id;

    function __construct($id) {
        $this->id = $id;
    }

    public function addGalleryColumn() {
        if (! $this->checkGalleryColumn()) {
            $item = new \stdClass();
            $item->id = $this->id;
            $item->gallery = '{gallery}'.$this->id.'{/gallery}';
            return \JFactory::getDbo()->updateObject('#__k2_items', $item, 'id');
        }
    }

    public function deleteGalleryColumn() {
        $item = new \stdClass();
        $item->id = $this->id;
        $item->gallery = '';
        return \JFactory::getDbo()->updateObject('#__k2_items', $item, 'id');
    }

    protected function checkGalleryColumn() {
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('gallery');
        $query->from($db->quoteName('#__k2_items'));
        $query->where($db->quoteName('id')." = ".$db->quote($this->id));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return true;
        }
        return false;
    }

}
