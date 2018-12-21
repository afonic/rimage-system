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

    public function getK2PluginId() {
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id');
        $query->from($db->quoteName('#__extensions'));
        $query->where($db->quoteName('type')." LIKE ".$db->quote('plugin'));
        $query->where($db->quoteName('element')." LIKE ".$db->quote('rimage'));
        $query->where($db->quoteName('folder')." LIKE ".$db->quote('k2'));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return $result;
        }
        return false;
    }

}
