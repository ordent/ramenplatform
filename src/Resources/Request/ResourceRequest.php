<?php

namespace Ordent\Ramenplatform\Resources\Request;

use Illuminate\Foundation\Http\FormRequest;
use Ordent\Ramenplatform\Resources\Request;
use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;

class ResourceRequest extends FormRequest
{

    protected $model;
    protected $modelPath = 'Ordent\Ramenplatform\Resources\Model\ResourceModel';
    protected $key;
    public function __construct(ResourceModelInterface $model = null, $key = null){
        if(is_null($model)){
            $this->model = app($this->modelPath);
        }else{
            $this->model = $model;
        }
        $this->key = $key;

        $this->container = app('Illuminate\Container\Container');
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        // authorization code here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        if(is_null($this->model)){
            return [];
        }
        return $this->model->getRules($this->key);
    }

    public function getModel(){
        return $this->model;
    }
}
