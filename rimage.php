<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
require('vendor/autoload.php');

use Reach\Views;
use Reach\Order;
use Reach\Upload;

class plgSystemRImage extends JPlugin
{

    function onAfterInitialise() {
    	// Include the autoloader for the plugins
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
                echo new JResponseJson();
                jexit();
            }
        }

        if ($app->isAdmin() and ($app->input->get('rimage') == 'upload')) {
            $id = $app->input->get('rid');
            $file = $app->input->files->get('file');
            if ($id && $file) {
                $this->handleUpload($id, $file);
            }
        }        

        if ($app->isAdmin() and ($app->input->get('rimage') == 'delete')) {
            $id = $app->input->get('rid');
            $file = $app->input->get('rfile', null, RAW);
            if ($id && $file) {
                $this->handleDelete($id, $file);
            }
        }

        if ($app->isAdmin() and ($app->input->get('rimage') == 'order')) {
            $id = $app->input->get('rid');
            $order = $app->input->get('rdata', null, RAW);
            if ($id && $order) {
                $this->handleOrder($id, $order);
            }
        }
    }

    function handleOrder($id, $array) {
        try
        {
            $order = new Order($id);
            $order->saveOrderJson($array);
            echo new JResponseJson();
            jexit();
        }
        catch (Exception $e)
        {
            header("HTTP/1.0 500 Error");
            echo new JResponseJson($e);
            jexit();
        }
    }        

    function handleUpload($id, $file) {
        try
        {
            $upload = new Upload($id);
            $upload->handle($file);
            echo new JResponseJson();
            jexit();
        }
        catch (Exception $e)
        {
            header("HTTP/1.0 500 Error");
            echo new JResponseJson($e);
            jexit();
        }
    }    

    function handleDelete($id, $file) {
        try
        {
            unlink(JPATH_ROOT.$file);
            $order = new Order($id);
            $order->removeFromOrderArray($file);
            echo new JResponseJson();
            jexit();
        }
        catch (Exception $e)
        {
            header("HTTP/1.0 500 Error");
            echo new JResponseJson($e);
            jexit();
        }
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
