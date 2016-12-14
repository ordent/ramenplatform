<?php namespace Ordent\Ramenplatform\Resources;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Ordent\Ramenplatform\Resources\Response\ResourceResponse;
use Illuminate\Http\UploadedFile;

class RamenResource{
    protected $response;

    public function __construct(ResourceResponse $response){
        $this->response = $response;
    }

    public function index($model, $param){
        // setup
        $param = $this->resolveParam($param);

        $model = $this->resolveModel($model);

        //query builder
        $query = $model;
        // get array of filter from model
        foreach ($model->getIndexFilters() as $filter) {
            //get query from model (always return query)
            $query = $query->$filter($param);
        }

        // call query
        $results = $query->get();
        // transform

        return $this->response->makeResponse(200, "", $results, $model->getTransformer());
    }

    public function detail($model, $id){

        $model = $this->resolveModel($model);
        $results = $model->find($id);
        if($results){
            return $this->response->makeResponse(200, "", $results, $model->getTransformer());
        }

        return $this->response->makeResponse(404);
    }

    public function store($model, $data){

        $model = $this->resolveModel($model);
        $results = $model->fill($data);
        foreach($results->getFileList() as $file){
          $path = $model->$file->store($model->getUploadPath());
          $results->$file = $path;
        }
        if($results && $results->save()){
            return $this->response->makeResponse(200, "", $results, $model->getTransformer());
        }

        return $this->response->makeResponse(404);

    }

    public function update($model, $data, $id = null){

        if(is_null($id)){
            return $this->store($model, $data);
        }

        $model = $this->resolveModel($model);

        $entity = $model->find($id);
        if(!$entity) return $this->response->makeResponse(404);
        foreach($results->getFileList() as $file){
          $path = $model->$file->store($model->getUploadPath());
          $results->$file = $path;
        }
        $results = ($entity->update($data)) ? $entity : false;

        if($results){
            return $this->response->makeResponse(200, "", $results, $model->getTransformer());
        }

        return $this->response->makeResponse(404);
    }

    public function delete($model, $id, $soft = 0){
        $model = $this->resolveModel($model);

        $entity = $model->find($id);

        if(!$entity) return $this->response->makeResponse(404);
        //if $soft = 1 execute soft delete else force delete
        $results = ($entity->delete()) ? $entity : false;
        if($results){
            return $this->response->makeResponse(200, "", $results, $model->getTransformer());
        }

        return $this->response->makeResponse(404);
    }

    protected function resolveModel($model){
        if(! $model instanceof Model){
            $model = app($model);
        }

        return $model;
    }

    protected function resolveParam($param){
        if($param instanceof Request){
            $param = $param->query();
        }
        return $param;
    }
}
