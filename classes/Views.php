<?php

namespace Reach\RImage;

use Reach\RImage\DatabaseHelper;
use Reach\rImageFiles;

// This hackish class handles displaying some HTML at the backend
class Views {

    protected $id;
    protected $item;
    protected $files;

    public function __construct($item) {
        $this->id = $item->id;
        $this->item = $item;
        $this->files = (new rImageFiles($item->id))->getFiles();
        $this->dir = (new rImageFiles($item->id))->getDir();
    }

    protected function images() {
        $images = '<div class="rthumbs-noimages">No images. Add some!</div>';
        if ($this->files) {
            $images = '';
            foreach ($this->files as $file) {
                $path = str_replace(JPATH_ROOT, '', $file->path);
                $name = str_replace($this->dir, '', $file->path);
                $images .= '
                    <div class="rthumb" data-id="'.$path.'">
                        <div class="rthumb-image"><img data-src="'.$path.'" src="/plugins/system/rimage/assets/empty.png" /></div>
                        <div class="rthumb-label">'.$name.'</div>
                        <div class="rthumb-delete"><span class="icon-delete"></span></div>
                    </div>
                    '.PHP_EOL;
            }
        }
        return $images;
    }

    public function modalManager() {
        return '
        <div id="rimage-manage" data-rid="'.$this->id.'" data-rtoken="'.\JSession::getFormToken().'" class="rimage-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="Manage Gallery">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Manage gallery</h4>
                    <span>Drag to reorder, order is saved automatically.</span>
                </div>
                <div class="modal-body">
                    <div id="rthumbs" class="rthumbs-container">'.$this->images().'</div>
                    <div class="upload-images-container">
                        <div class="dropzone" id="upload-images"></div>
                        <div class="upload-buttons">
                            <button type="button" class="btn btn-save" onclick="Joomla.submitbutton(\'apply\');"><span class="icon-save" aria-hidden="true"></span> Save & Close</button>
                            <button type="button" class="btn btn-error" id="upload-images-cancel"><span class="icon-cancel" aria-hidden="true"></span> Cancel</button>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="add-images"><span class="icon-upload" aria-hidden="true"></span> Add images</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="icon-cancel" aria-hidden="true"></span> Close</button>
                </div>
            </div>
          </div>
        </div>
        ';
    }

    public function modalPlugin() {
        $pluginId = (new DatabaseHelper($this->id))->getK2PluginId();
        $url = \JURI::base().'index.php?option=com_plugins&task=plugin.edit&extension_id='.$pluginId;
        return '
        <div id="rimage-plugin" class="rimage-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="Manage Plugin">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Plugin options</h4>
                </div>
                <div id="rimage-plugin-container" class="modal-body">
                <iframe id="rimage-plugin-iframe" data-src="'.$url.'" src="about:blank">
                </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="icon-cancel" aria-hidden="true"></span> Close</button>
                </div>
            </div>
          </div>
        </div>
        ';
    }

    public function modalRegenerate() {
        return '
        <div id="rimage-regenerate" class="rimage-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="Regenerate images">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Regenerate all images</h4>
                </div>
                <div class="modal-body">
                    <div class="message">Please note that this window regenerates <strong>ALL images in your website</strong>.
                    It should be used when you have changed the image sets and need to regenerate everything. It is extremely
                    resource intensive and should be used with care.<br>You are about to regenerate the images for <span class="total-to-regen" id="items-number"></span> items.</div>
                    <button id="confirm-regenerate" type="button" class="btn btn-default"><span class="icon-loop" aria-hidden="true"></span> Confirm Regenerate All</button>
                    <div id="regen-bar"><div id="regen-progress"></div></div>
                    <div id="counter"><span class="current">0</span> / <span class="total-to-regen"></span></div>
                </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="icon-cancel" aria-hidden="true"></span> Close</button>
                </div>
            </div>
          </div>
        </div>
        ';
    }

    public function regenerateButton() {
        $options = array();
        $options[] = 'data-id="'.$this->item->id.'"';
        $options[] = 'data-category="'.$this->item->catid.'"';
        $options[] = 'data-rtoken="'.\JSession::getFormToken().'"';
        if ($this->item->gallery) {
            $options[] = 'data-gallery="yes"';
        }
        return '<div id="rimage-options" '.implode(' ', $options).' style="display: none"></div>';
    }

}
