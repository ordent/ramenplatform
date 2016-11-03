<?php namespace Ordent\Ramenplatform\Resources\Traits;

use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;
use Ordent\Ramenplatform\Resources\Request\ResourceRequest;
use Ordent\Ramenplatform\Resources\Request\ResourceRequestFactory;
use Ordent\Ramenplatform\Resources\RamenResource;
use Ordent\Ramenplatform\Resources\Response\ResourceResponse;
use Illuminate\Validation\ValidationException;

trait ResourceControllerTrait{
    protected $model;
    protected $modelPath = "Ordent\Ramenplatform\Resources\Model\ResourceModel";
    protected $resource;
    protected $response;
    public function __construct(ResourceModelInterface $model = null, ResourceResponse $response = null){
        if(is_null($model)){
            $this->model = app($this->modelPath);
        }else{
            $this->model = $model;
        }
        $this->resource = app('Ordent\Ramenplatform\Resources\RamenResource');
        if(is_null($response)){
            $response = app('Ordent\Ramenplatform\Resources\Response\ResourceResponse');
        }
        $this->response = $response;

    }

    public function index(ResourceRequest $request = null){
        if(is_null($request) || $request->getModel() != $this->getModel()){
            // factory pattern
            $request = ResourceRequestFactory::createRequest($this->model);
        }
        return $this->resource->index($this->model, $request);
    }

    public function show($id, ResourceRequest $request = null){
        if(is_null($request) || $request->getModel() != $this->getModel()){
            $request = ResourceRequestFactory::createRequest($this->model);
        }

        return $this->resource->detail($this->model, $id);
    }

    public function store(ResourceRequest $request = null){
        try{
            if(is_null($request) || $request->getModel() != $this->getModel()){
                $request = ResourceRequestFactory::createRequest($this->getModel(), 'store');
            }
        }catch(ValidationException $e){
            $response = $this->response->makeResponse(422, $e->validator->getMessageBag()->all());
        }

        return $this->resource->store($this->getModel(), $request->all());

    }

    public function update($id, ResourceRequest $request = null){
        try{
            if(is_null($request) || $request->getModel() != $this->getModel()){
                $request = ResourceRequestFactory::createRequest($this->getModel(), 'update');
            }
        }catch(ValidationException $e){
            $response = $this->response->makeResponse(422, $e->validator->getMessageBag()->all());
        }

        return $this->resource->update($this->getModel(), $request->all(), $id);
    }

    public function delete($id, ResourceRequest $request = null){
        try{
            if(is_null($request) || $request->getModel() != $this->getModel()){
                $request = ResourceRequestFactory::createRequest($this->getModel(), 'delete');
            }
        }catch(ValidationException $e){
            $response = $this->response->makeResponse(422, $e->validator->getMessageBag()->all());
        }

        return $this->resource->delete($this->getModel(), $id);
    }

    // for implementing view

    public function viewIndex(){

    }

    public function viewShow(){

    }

    public function viewCreate(){

    }

    public function viewEdit(){

    }

    public function setModel(ResourceModelInterface $model){
        $this->model = $model;
    }

    public function getModel(){
        return $this->model;
    }
}
