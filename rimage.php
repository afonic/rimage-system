<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
require JPATH_ROOT.'/plugins/system/rimage/vendor/autoload.php';

use Reach\RImage\Views;
use Reach\RImage\RImageController;

class plgSystemRImage extends JPlugin
{
    /**
     * Joomla onAfterInitialise global event.
     *
     * @return null
     */
    public function onAfterInitialise()
    {
        // Include the autoloader for the K2 plugin
        include_once JPATH_ROOT.'/plugins/k2/rimage/vendor/autoload.php';

        // Let the controller handle the nasty business
        $controller = new RImageController(JFactory::getApplication(), JFactory::getUser());
    }

    /**
     * Joomla event that fires when a K2 admin form is loaded.
     *
     * @param object $item The K2 item
     * @param string $type The type of the form.
     * @param string $tab  The K2 tab
     *
     * @return null
     */
    public function onRenderAdminForm(&$item, $type, $tab = '')
    {
        if (($item->id) && ($type == 'item') && ($tab == 'content')) {
            $this->renderManager($item);
            if ($item->gallery) {
                $this->renderRegenerate($item);
            }
            if (($this->params['hidesigpro'] != '1')) {
                $doc = JFactory::getDocument();
                $doc->addScriptDeclaration(
                    'jQuery(document).ready(function($) {
                        $("li#tabImageGallery").remove();
                        $("#k2TabImageGallery").remove();
                    });'
                );
            }
        }
    }

    /**
     * This function echoes the regenerate related HTML.
     *
     * @param object $item The K2 object item.
     *
     * @return null
     */
    public function renderRegenerate($item)
    {
        $views = new Views($item);
        echo $views->regenerateButton();
        echo $views->modalRegenerate();

        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/regen.js');
    }

    /**
     * This function echoes the gallery manager related HTML.
     * 
     * @param object $item The K2 object item.
     * 
     * @return null
     */
    public function renderManager($item)
    {
        $views = new Views($item);
        echo $views->modalManager();
        echo $views->modalPlugin();

        $doc = JFactory::getDocument();
        $doc->addScript('/plugins/system/rimage/assets/notify.js');
        $doc->addScript('/plugins/system/rimage/assets/sortable.js');
        $doc->addScript('/plugins/system/rimage/assets/dropzone.js');
        $doc->addScript('/plugins/system/rimage/assets/manage.js');
        $doc->addStylesheet('/plugins/system/rimage/assets/dropzone.css');
        $doc->addStylesheet('/plugins/system/rimage/assets/manage.css');
    }
}
