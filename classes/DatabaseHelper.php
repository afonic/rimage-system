<?php

namespace Reach\RImage;

use Reach\rImageDbHelper;

// Helper functions for database calls
class DatabaseHelper {

    protected $id;
    protected $db;

    public function __construct($id = null) {
        $this->id = $id;
        $this->db = \JFactory::getDbo();
    }

    public function addGalleryColumn() {
        if (! $this->checkGalleryColumn()) {
            $item = new \stdClass();
            $item->id = $this->id;
            $item->gallery = '{gallery}'.$this->id.'{/gallery}';
            return $this->db->updateObject('#__k2_items', $item, 'id');
        }
    }

    public function deleteGalleryColumn() {
        $item = new \stdClass();
        $item->id = $this->id;
        $item->gallery = '';
        return $this->db->updateObject('#__k2_items', $item, 'id');
    }

    protected function checkGalleryColumn() {
        $db = $this->db;
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
        $db = $this->db;
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

    public function getItemsToRegenerate() {
        $categories = implode(',',  $this->getCategoriesToRegenerate());
        $db = $this->db;
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id', 'catid', 'gallery')));
        $query->from($db->quoteName('#__k2_items'));
        $query->where($db->quoteName('published') . ' = 1');
        $query->where($db->quoteName('trash') . ' != 1');
        $query->where($db->quoteName('catid') . ' IN ('. $categories .')');
        $db->setQuery($query);
        $items = $db->loadAssocList();
        foreach ($items as $item) {
            if ($item['gallery']) {
                $item['gallery'] = 1;
            } else {
                $item['gallery'] = 0;
            }
        }
        return $items;
    }

    protected function getCategoriesToRegenerate() {
        $sets = $this->getItemSets()['image-sets'];
        $childCategories = $this->getChildK2Categories();
        $categories = array();
        foreach ($sets as $set) {
            foreach ($set['k2categories'] as $catId) {
                if (! in_array($catId, $categories)) {
                    $categories[] = $catId;
                }
                if ($set['k2selectsubcategories'] == '1') {
                    foreach ($childCategories as $child) {
                        if ((! in_array($child->id, $categories)) && ($child->parent == $catId)) {
                            $categories[] = $child->id;
                        } 
                    }
                }
            }
        }
        return $categories;
    }

    // Get all parent categories from database
    protected function getChildK2Categories() {
        $db = $this->db;
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id', 'parent')));
        $query->from($db->quoteName('#__k2_categories'));
        $query->where($db->quoteName('published') . ' = 1');
        $query->where($db->quoteName('parent') . ' != 0');
        $db->setQuery($query);
        return $db->loadObjectList('id');
    }
    
    // Get all item sets
    protected function getItemSets() {
        $db = $this->db;
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('params')));
        $query->from($db->quoteName('#__extensions'));
        $query->where($db->quoteName('element') . ' = '. $db->quote('rimage'), 'AND');
        $query->where($db->quoteName('folder') . ' = '. $db->quote('k2'));
        $db->setQuery($query);
        return json_decode($db->loadResult(), true);
    }

}
