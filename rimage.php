<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
require('vendor/autoload.php');

use Reach\Views;
use Reach\RimageController;

class plgSystemRImage extends JPlugin
{

    function onAfterInitialise() {
    	// Include the autoloader for the plugins
        require_once(JPATH_ROOT.'/plugins/k2/rimage/vendor/autoload.php');
        
        // Let the controller handle the nasty business
        $controller = new RimageController(JFactory::getApplication(), JFactory::getUser());     

    }

    function onRenderAdminForm(&$item, $type, $tab = '') {
        if (($item->id) && ($type == 'item') && ($tab == 'content')) {
            $this->renderManager($item);
            if ($item->gallery) {            
                $this->renderRegenerate($item);            
            }
            if (($this->params['hidesigpro'] != '1')) {
                $doc = JFactory::getDocument();
                $doc->addScriptDeclaration('
                jQuery(document).ready(function($) {
                    $("li#tabImageGallery").remove();
                    $("#k2TabImageGallery").remove();
                });
                ');
            }
        }
    }

    function renderRegenerate($item) {
        $options = array();
        $options[] = 'data-id="'.$item->id.'"';
        $options[] = 'data-category="'.$item->catid.'"';
        $options[] = 'data-rtoken="'.JSession::getFormToken().'"';
        if ($item->gallery) {
            $options[] = 'data-gallery="yes"';
        }
        echo '<div id="rimage-options" '.implode(' ', $options).' style="display: none"></div>';
        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/regen.js');
    }

    function renderManager($item) {
        $views = new Views($item->id);
        echo $views->modal();

        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/notify.js');
        $doc->addScript('/plugins/system/rimage/assets/sortable.js');
        $doc->addScript('/plugins/system/rimage/assets/dropzone.js');
        $doc->addScript('/plugins/system/rimage/assets/manage.js');
        $doc->addStylesheet('/plugins/system/rimage/assets/dropzone.css');
        $doc->addStylesheet('/plugins/system/rimage/assets/manage.css');
    }

}
