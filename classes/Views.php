<?php

namespace Reach;

use Reach\rImageFiles;

// This hackish class handles displaying some HTML at the backend
class Views {

	protected $id;
	protected $files;

	function __construct($id) {
		$this->id = $id;
		$this->files = (new rImageFiles($id))->getFiles();
		$this->dir = (new rImageFiles($id))->getDir();
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
                        <div class="rthumb-image"><img src="'.$path.'" /></div>
                        <div class="rthumb-label"><span class="name">'.$name.'</span></div>
                        <div class="rthumb-delete"><span class="icon-delete"></span></div>
                    </div>
                    '.PHP_EOL;
            }
        }
        return $images;
	}

	public function modal() {
		return '
        <div id="rimage-manage" data-rid="'.$this->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Order Gallery">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 16px">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Manage gallery</h4>
                    <span>Drag to reorder, order is saved automatically.</span>
                </div>
                <div class="modal-body" style="width: calc(100% - 1rem); padding: 0.5rem">
                    <div id="rthumbs" class="rthumbs-container">'.$this->images().'</div>
                    <div class="upload-images-container">
                        <div class="dropzone" id="upload-images"></div>
                        <div class="upload-buttons">
                            <button type="button" class="btn btn-success" onclick="Joomla.submitbutton(\'apply\');">Save and close</button>
                            <button type="button" class="btn btn-error" id="upload-images-cancel">Cancel</button>
                            </div>
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="add-images">Add images</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>
        ';
	}

}
