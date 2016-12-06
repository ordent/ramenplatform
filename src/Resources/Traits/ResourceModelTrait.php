<?php namespace Ordent\Ramenplatform\Resources\Traits;


trait ResourceModelTrait{

    public function setTransformer($transformer){
        if($transformer instanceof \Ordent\Ramenplatform\Resources\Transformer\ResourcesTransformer){
            $this->transformers = $transformer;
        }else{
          if(is_string($transformer)){
            $this->transformers = app($transformer);
          }
        }
    }

    public function getTransformer(){
        if(!$this->transformers instanceof \Ordent\Ramenplatform\Resources\Transformer\ResourcesTransformer){
            $this->setTransformer($this->transformers);
        }
        return $this->transformers;
    }

    public function setRules($key, Array $attributes, $appends = false){
        if($appends){
            $this->rules[$key] = array_merge($this->rules[$key], $attributes);
        }else{
            $this->rules[$key] = $attributes;
        }
    }

    public function getRules($key){
        if(array_key_exists($key, $this->rules)){
            return $this->rules[$key];
        }
        return [];
    }

    public function setUploadPath($path){
        $this->uploadPath = $path;
    }

    public function getUploadPath(){
        return $this->uploadPath;
    }

    public function hasSlug(){
        if(array_key_exists("slug", $this->attributes)){
            return true;
        }
        return false;
    }

    public function getTransformedData(){
        $results = $this->toArray();
        // foreach($this->getAttributes() as $attributes){
        //     $results[$attributes] = $this->$attributes;
        // }

        return $results;
    }

    //set or add scope as index filters
    public function setIndexFilters($scope, $appends = false){
        if($appends){
            $this->indexFilters = array_merge($this->indexFilters, (array) $scope);
        }else{
            $this->indexFilters = (array) $scope;
        }
    }

    //get list of index filters
    public function getIndexFilters(){

        return $this->indexFilters;
    }

    //scope for limit & offset
    public function scopePagination($query, $input){

        if (isset($input['limit']) && isset($input['offset'])){
            $query = $query->limit((int) $input['limit'])->offset((int) $input['offset']);
        }

        return $query;
    }

    //scope for sort data
    public function scopeSort($query, $input){

        if  (isset($input['sort'])){
            $query = $query->orderBy('id', $input['sort']);
        }

        return $query;
    }

    public function getFileList(){
      return $this->files;
    }

    public function fill(array $attributes){
      $results = parent::fill($attributes);

      foreach($this->getFileList() as $file){
        if($results->$file instanceof UploadedFile){
          $results->$file = $this->saveFile($results, $results->file);
        }
      }

      return $results;
    }

}
