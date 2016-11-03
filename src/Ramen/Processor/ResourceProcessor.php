<?php

namespace Ordent\Ramenplatform\Ramen\Processor;
use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;
use Ordent\Ramenplatform\Resources\Model\ResourceModel;
use Ordent\Ramenplatform\Resources\Request\ResourceRequest;
use Ordent\Ramenplatform\Resources\Request\ResourceRequestFactory;
use Ordent\Ramenplatform\Resources\Response\ResourceResponse;
class ResourceProcessor{
    protected $model;
    protected $request;
    protected $response;

    public function __construct(ResourceResponse $response){
        $this->response = $response;
    }

    public function process(){

    }

    public function setModel($model){
        if(is_null($model)){
            $model =  new ResourceModel;
        }

        if(!$model instanceof ResourceModelInterface){
            $model = app($model);
        }

        $this->model = $model;
    }

    public function getModel(){
        if(is_null($this->model)){
            $this->setModel();
        }
        return $this->model;
    }

    public function setRequest($request){
        if(is_null($request)){
            $request = ResourceRequestFactory::createRequest($this->getModel());
        }

        $this->request = $request;
    }

    public function getRequest(){
        return $this->request;
    }

}
