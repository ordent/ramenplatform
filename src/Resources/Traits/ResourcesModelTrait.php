<?php namespace Ordent\Ramenplatform\Resources\Traits;


trait ResourcesModelTrait{
    /**
     * attributes of a model
     * @var array of attributes name on string
     */
    protected $attributes = []
    /**
     * casting of a attributes to native types, useful for native types, like integer, boolean, JsonSerializable
     * @var ["attributes"=>"type"]
     */
    protected $casts = [];
    /**
     * casting of a date attributes
     * @var ["attributes1", "attributes2"]
     */
    protected $dates = [];

    /**
     * original state of an attributes
     * @var ["isActive" => 1]
     */
    protected $original = [];

    /**
     * set the transformer file of the model
     * @var string
     */
    protected $transformers = "";

    /**
     * set the rules of an attributes
     * @var [type]
     */
    protected $rules = [
        "store" => [],
        "update" => [],
        "delete" => []
    ];

    protected $uploadPath;

    public function setTransformer($transformer){
        if($transformer instanceof Ordent\Ramenplatform\Resources\Transformer\ResourcesTransformer){
            $this->transformers = $transformer;
        }else{
            $this->transformers = app($transformer);
        }
    }

    public function getTransformer(){
        return $this->transformer;
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
        return false;
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
        $results = $this->getMutatedAttributes();
        // foreach($this->getAttributes() as $attributes){
        //     $results[$attributes] = $this->$attributes;
        // }

        return $results;
    }
}
