<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemRImage extends JPlugin
{
    // function __construct(&$subject, $params)
    // {
    //     parent::__construct($subject, $params);
    // }

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
    }

    function onRenderAdminForm(&$item, $type, $tab = '') {
        if ($this->params['showregen'] != '1') {
            if (($item->id) && ($type == 'item') && ($tab == 'content')) {
                $this->render($item);
            }
        }
    }

    function render($item) {
        $options = array();
        $options[] = 'data-id="'.$item->id.'"';
        $options[] = 'data-category="'.$item->catid.'"';
        if ($item->gallery) {
            $options[] = 'data-gallery="yes"';
        }
        echo '<div id="rimage-options" '.implode(' ', $options).' style="display: none"></div>';
        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/script.js');
    }

}
