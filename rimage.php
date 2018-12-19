<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemRImage extends JPlugin
{

    function onAfterInitialise() {
    	// Include the autoloader for the plugin
        require_once(JPATH_ROOT.'/plugins/k2/rimage/vendor/autoload.php');

        // Run the regenerator
        $app = JFactory::getApplication();

        if ($app->isAdmin() and ($app->input->get('rimage') == 'regen')) {
            $regenerator = new Reach\rImageForceRegeneration;
            $regenerator->regenerate();
        }        

        if ($app->isAdmin() and ($app->input->get('rimage') == 'item')) {
            $id = $app->input->get('rid');
            $catid = $app->input->get('rcatid');
            $gallery = $app->input->get('rgallery');
            if ($id && $catid) {
                $regenerator = new Reach\rImageForceRegeneration;
                $regenerator->regenerateSingle($id, $catid, $gallery);
            }
        }        

        if ($app->isAdmin() and ($app->input->get('rimage') == 'order')) {
            $id = $app->input->get('rid');
            $order = $app->input->get('rdata', null, PATH);
            if ($id && $order) {
                $this->saveOrderJson($id, $order);
            }
        }
    }

    function onRenderAdminForm(&$item, $type, $tab = '') {
        if (($item->id) && ($type == 'item') && ($tab == 'content')) {
            if (($this->params['showregen'] != '1') || ($this->params['showorder'] != '1')) {
                $this->renderNotify();
            }
            if ($this->params['showregen'] != '1') {            
                $this->renderRegenerate($item);            
            }
            if ($item->gallery && ($this->params['showorder'] != '1')) {
                $this->renderOrder($item);
            }            
        }
    }

    function renderNotify() {
        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/notify.js');
    }

    function renderRegenerate($item) {
        $options = array();
        $options[] = 'data-id="'.$item->id.'"';
        $options[] = 'data-category="'.$item->catid.'"';
        if ($item->gallery) {
            $options[] = 'data-gallery="yes"';
        }
        echo '<div id="rimage-options" '.implode(' ', $options).' style="display: none"></div>';
        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/regen.js');
    }

    function renderOrder($item) {
        $files = $this->getImages($item->id);
        $images = '';
        foreach ($files as $file) {
            $path = str_replace(JPATH_ROOT, '', $file->path);
            $images .= '<div class="rthumb" data-id="'.$path.'"><img src="'.$path.'" /></div>'.PHP_EOL;
        }
        $modal = '
        <div id="rimage-order" data-rid="'.$item->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Order Gallery">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 16px">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Order gallery items</h4>
                    <span>Drag to reorder, order is saved automatically.</span>
                </div>
                <div class="modal-body" style="width: calc(100% - 1rem); padding: 0.5rem">
                    <div id="rthumbs" class="rthumbs-container">'.$images.'</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>
        ';
        echo $modal;
        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/sortable.js');
        $doc->addScript('/plugins/system/rimage/assets/order.js');
        $doc->addStylesheet('/plugins/system/rimage/assets/order.css');
    }

    function getImages($id) {
        $files = new Reach\rImageFiles($id);
        return $files->getFiles();
    }    

    function getDir($id) {
        $files = new Reach\rImageFiles($id);
        return $files->getDir();
    }

    function saveOrderJson($id, $order) {
        $path = $this->getDir($id).'order.json';
        $json_data = json_encode($order);

        if (file_exists($path)) {
            unlink($path);
        }

        if (!file_put_contents($path, $json_data)) {
            JFactory::getApplication()->enqueueMessage("Couldn't write JSON file! Are you sure the path is correct?", 'error');
        }
        else {
            JFactory::getApplication()->enqueueMessage("JSON generation OK.", 'message');
        }
    }

}
