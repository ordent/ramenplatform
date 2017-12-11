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
		// dd($model->getCountForPagination(["*"])->get());
		// get array of filter from model

		foreach ($model->getIndexFilters() as $filter) {
			//get query from model (always return query)
			
			if(!$query instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator){
				if($filter == "where"){
					if(!array_key_exists("datatables", $param)){
						$query = $query->$filter($param);
					}
				}else{
					$query = $query->$filter($param);
				}

			}
			// if($filter == "datatables"){
			//     dd($query);
			// }
		}
		$itotal = $query->get()->count();

		if(array_key_exists("datatables", $param)){
			if(array_key_exists("start", $param)){
				$query = $query->offset($param['start']);
			}

			if(array_key_exists("length", $param)){
				$query = $query->limit($param['length']);
			}
		}

		$meta = [];


		if((!$query instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)){
			$results = $query->get();				
		}else{
			$results = $query;
		}
		$filteredTotal = $results->count();
		// call query
		//$results = $query->get();
		// transform
		if(array_key_exists("datatables", $param)){
			$meta["datatables"] = true;
			$meta["draw"] = 1;
			if(array_key_exists("sEcho", $param)){
				$meta["sEcho"] = $param["sEcho"];
			}
			if(array_key_exists("draw", $param)){
				$meta["draw"] = (int) $param["draw"];
			}
			$meta['iTotalRecords'] = $itotal;
			$meta['recordsTotal'] = $itotal;
			$meta["iTotalDisplayRecords"] = $filteredTotal;
			$meta["recordsFiltered"] = $filteredTotal;
		}
		return $this->response->makeResponse(200, "", $results, $model->getTransformer(), $meta);
	}

	public function detail($model, $id){

		$model = $this->resolveModel($model);
		if(is_numeric($id)){
			$results = $model->find($id);
		}else{
			$results = $model->where("slug", $id)->get()->first();
		}

		if($results){
			return $this->response->makeResponse(200, "", $results, $model->getTransformer());
		}

		return $this->response->makeResponse(404);
	}

	public function store($model, $data){

		$model = $this->resolveModel($model);
		$results = $model->fill($data);

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
