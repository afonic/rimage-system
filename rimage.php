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
    }
    
    function onAfterRoute() {
        JHtml::_('jquery.framework');
    }

}
