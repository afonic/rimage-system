<?php

namespace Reach\RImage;

use Reach\RImage\Order;
use Reach\RImage\Upload;
use Reach\RImage\Delete;
use Reach\RImage\DatabaseHelper;
use Reach\rImageForceRegeneration;

/**
 * Make believe controller. To be injected at the onAfterInitialise event.
 */
class RImageController
{
    protected $app;
    protected $task;
    protected $input;
    protected $user;
    protected $validTasks;

    /**
     * Class constructor.
     *
     * @param \Joomla\CMS\Application\CMSApplicationobject $app  Joomla application
     * @param \Joomla\CMS\User\Userobject $user  Global user object
     */
    public function __construct($app, $user)
    {
        $this->app = $app;
        $this->task = $app->input->get('rimage');
        $this->input = $app->input;
        $this->user = $user;
        // Set the allowed tasks here.
        $this->validTasks = ['upload', 'order', 'regenitem', 'regen', 'delete'];
        $this->execute();
    }

    /**
     * Executes the controller, and if authorized, runs the appropriate method.
     * 
     * @return null
     */
    protected function execute()
    {
        if (in_array($this->task, $this->validTasks)) {
            // Check if we are in the admin panel
            $this->checkIfInAdmin();
            // Authorize user
            $this->authorize();
            // Send it to the appropriate action
            $this->{$this->task}();
        }
    }

    /**
     * Handle changing the order of the gallery.
     * 
     * @return null
     */
    protected function order()
    {
        $this->checkToken();
        $id = $this->input->get('rid');
        $array = $this->input->get('rdata', null, RAW);
        if ($id && $array) {
            try {
                $order = new Order($id);
                $order->saveOrderJson($array);
                echo new \JResponseJson();
                jexit();
            } catch (\Exception $e) {
                header("HTTP/1.0 500 Error");
                echo new \JResponseJson($e);
                jexit();
            }
        }
    }

    /**
     * Handle regenerating all images for an item.
     * 
     * @return null
     */
    protected function regenitem()
    {
        $this->checkToken();
        $id = $this->input->get('rid');
        $catid = $this->input->get('rcatid');
        $gallery = $this->input->get('rgallery');
        if ($id && $catid) {
            $regenerator = new rImageForceRegeneration;
            $regenerator->regenerateSingle($id, $catid, $gallery);
            echo new \JResponseJson();
            jexit();
        }
    }

    /**
     * Return an array of all K2 item's that belong at an image set in order
     * and would need to be regenerated.
     * 
     * @return null
     */
    protected function regen()
    {
        try {
            $helper = new DatabaseHelper;
            $items = $helper->getItemsToRegenerate();
            echo new \JResponseJson($items);
            jexit();
        } catch (Exception $e) {
            header("HTTP/1.0 500 Error");
            echo new \JResponseJson($e);
            jexit();
        }
    }

    /**
     * Handle deleting an image from the gallery.
     * 
     * @return null
     */
    protected function delete()
    {
        $this->checkToken();
        $id = $this->input->get('rid');
        $file = $this->input->get('rfile', null, RAW);
        if ($id && $file) {
            try {
                $delete = new Delete($id);
                $delete->handle($file);
                echo new \JResponseJson();
                jexit();
            } catch (Exception $e) {
                header("HTTP/1.0 500 Error");
                echo new \JResponseJson($e);
                jexit();
            }
        }
    }

    /**
     * Handle uploading an image.
     * 
     * @return null
     */
    protected function upload()
    {
        $this->checkToken(true);
        $id = $this->input->get('rid');
        $file = $this->input->files->get('file');
        if ($id && $file) {
            try {
                $upload = new Upload($id);
                $upload->handle($file);
                echo new \JResponseJson();
                jexit();
            } catch (Exception $e) {
                header("HTTP/1.0 500 Error");
                echo new \JResponseJson($e);
                jexit();
            }
        }
    }

    /**
     * Check if the user is authorized. Right now the "create" permission for
     * K2 is hardcoded here.
     * 
     * @return null
     */
    protected function authorize()
    {
        if (! $this->user->authorise('core.create', 'com_k2')) {
            jexit('Not authorized');
        }
    }

    /**
     * Checks for a valid token of the request.
     *
     * @param bool $get In case this is a GET request, pass it to the JRequest class.
     * 
     * @return null
     */
    protected function checkToken($get = null)
    {
        if ($get) {
            if (! \JRequest::checkToken('get')) {
                var_dump('got here');
                jexit('Invalid token');
            }
        } else {
            if (! \JRequest::checkToken()) {
                jexit('Invalid token');
            }
        }
    }

    /**
     * Check if we are in the admin side of the Joomla site.
     * 
     * @return null
     */
    protected function checkIfInAdmin()
    {
        if (! $this->app->isAdmin()) {
            jexit('Only works in admin!');
        }
    }
}
