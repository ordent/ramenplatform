<?php

namespace Ordent\Ramenplatform\Ramen\Processor\Files;
use Ordent\Ramenplatform\Ramen\Processor\ResourceProcessor;

class FileStoreProcessor extends ResourceProcessor{

    public function process(){
        $files = $this->getRequest()->files;
        //fill name if its not already set
        if(!$this->getRequest()->has('name')){
            $this->getRequest()->name = $this->cleanFileName($files->getClientOriginalName());
        }
        // global processing if $this->getRequest() has set mimetype
        if(!$this->getRequest()->has('mimeType')){
            $this->getRequest()->mimeType = $files->getMimeType();
        }
        // if image
        if(count(config("ramen.image_mime_type"))>0){
            if(in_array(config("ramen.image_mime_type"))){
                $this->processImage();
            }
        }else{
            if(substr($this->getRequest()->mimeType, 0, 5) == "image"){
                $this->processImage();
            }
        }

        // if video
        if(count(config("ramen.video_mime_type"))>0){
            if(in_array(config("ramen.video_mime_type"))){
                $this->processVideo();
            }
        }else{
            if(substr($this->getRequest()->mimeType, 0, 5) == "video"){
                $this->processVideo();
            }
        }
        // if document

        if(count(config("ramen.document_mime_type"))>0){
            if(in_array(config("ramen.document_mime_type"))){
                $this->processDocument();
            }
        }
    }

    public function cleanFileName($name){
        $name = strtolower($name);
        $name = preg_replace("/[\W_]+/m", "-", $name );
        return $name;
    }

    public function processImage(){
        // image processing
        $image = \Intervention::make($this->getRequest()->files);

        if($this->getRequest()->has('image_width')){
            $width = $this->getRequest()->image_width;
        }else{
            $width = $image->width();
        }

        if($this->getRequest()->has('image_height')){
            $height = $this->getRequest()->image_height;
        }else{
            $height = $image->height();
        }

        $image->fit($width, $height);

        $name = date('Ymd').hash('md5', date('Y-m-d').$this->getRequest()->name);

        $path = $this->getModel()->getUploadPath();

        if($this->getRequest()->has('image_path')){
            $path = $this->getRequest()->image_path;
        }

        \Storage::put($path."/".$name, $image->stream($this->getRequest()->files->guessExtension()));

        $this->getModel()->name = $this->getRequest()->name;
        $this->getModel()->location = $this->getRequest()->location;
        $this->getModel()->mimeType = $this->getRequest()->mimeType;
        $this->getModel()->type = "image";
        $this->getModel()->save();

        return $this->response->makeResponse(200, "file uploaded successfully", $this->getModel(), $this->getModel()->getTransformer());
    }

    public function processVideo(){
        $video = Youtube::upload($pathToVideo, [
            'title'       => $this->getRequest()->name,
            'description' => $this->getRequest()->input('description', $this->getRequest()->name),
            'tags'        => $this->getRequest()->input('tags', []),
            'category_id' => 10
        ]);

        return $video->getVideoId();
    }

    public function processDocument(){

    }

    public function saveFile(){

    }
}
