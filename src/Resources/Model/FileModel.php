<?php namespace Ordent\Ramenplatform\Resources\Model;
use Ordent\Ramenplatform\Resources\Traits\ResourcesModelTrait;

class FileModel extends ResourceModel {

    use ResourceModelTrait;

    /**
     * attributes of a model
     * @var array of attributes name on string
     */
    protected $attributes = [
        'name',
        'location',
        'mimetype',
        'type'
    ];
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
        "store" => [
            'files' => 'required'
        ],
        "update" => [],
        "delete" => []
    ];

    /**
     * list of scope to be used as filter for index operation
     * @var array
     */
    protected $indexFilters = [];

    protected $uploadPath;

}
