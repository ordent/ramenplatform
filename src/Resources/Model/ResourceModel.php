<?php namespace Ordent\Ramenplatform\Resources\Model;

use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;
use Illuminate\Database\Eloquent\Model;
use Ordent\Ramenplatform\Resources\Traits\ResourceModelTrait;

class ResourceModel extends Model implements ResourceModelInterface{

    use ResourceModelTrait;

    /**
     * attributes of a model
     * @var array of attributes name on string
     */
    protected $attributes = [];
    protected $fillable = [];
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
    protected $transformers = "Ordent\Ramenplatform\Resources\Transformer\ResourceTransformer";

    /**
     * set the rules of an attributes
     * @var [type]
     */
    protected $rules = [
        "store" => [],
        "update" => [],
        "delete" => []
    ];

    /**
     * list of scope to be used as filter for index operation
     * @var array
     */
    protected $indexFilters = ['pagination', 'sort'];

    /*
    * list of attribute that have files type.
    */
    protected $files = [];
    protected $uploadPath = "uploads";

    public function __construct(){
      // if fillable attributes is empty, auto add fillable based on attributes array.
      if(count($this->fillable) == 0){
        foreach($this->attributes as $key => $attr){
          array_push($this->fillable, $key);
        }
      }
    }
}
