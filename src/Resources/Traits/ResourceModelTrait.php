<?php namespace Ordent\Ramenplatform\Resources\Traits;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


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
		if(isset($input['limit'])){
			if(isset($input['offset'])){
				$query = $query->limit((int) $input['limit'])->offset((int) $input['offset']);
			}elseif(isset($input['page'])){
				$offset = ($input['page'] - 1)* $input['limit'];
				$query = $query->limit((int) $input['limit'])->offset($offset);
			}elseif(isset($input['pagination'])){
				$query = $query->paginate($input['limit']);
			}elseif(isset($input['start'])){
				$query = $query->limit((int) $input['limit'])->offset($input['start'] - 1);
			}else{
				$query = $query->limit((int) $input['limit'])->offset(0);
			}
		}
		return $query;
	}

	public function scopeDatatables($query, $input){
		if(isset($input['search'])){
			if(isset($input['search']['value']) && $input["search"]["value"] != null){
				$attributes = [];
				foreach($this["attributes"] as $key => $attribute){
					$query = $query->orWhere($key, "like", "%".$input["search"]["value"]."%");
				}
				
				// dd($input["search"]["value"]);
			}
		}
		if(isset($input['datatables'])){
			if(isset($input['iDisplayStart'])){
				$query = $query->offset($input['iDisplayStart'] - 1);
			}
			if(isset($input['start'])){
				$query = $query->offset($input['start']);
			}

			if(isset($input['iDisplayLength'])){
				$query = $query->limit($input['iDisplayLength']);
			}

			if(isset($input['length'])){
				$query = $query->limit($input['length']);
			}
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
		$list = $this->getFileList();
		if(is_null($list)){
			$list = [];
		}

		foreach($list as $file){
			if(array_key_exists($file, $attributes)){
				if($attributes[$file] instanceof UploadedFile){
					$path = $this->processFile($attributes, $file);
					$this->$file = url(Storage::url($path));
				}
			}
		}
		return $results;
	}

	public function processFile($array, $key){
		$images = ["png", "jpg", "gif", "bmp", "jpeg"];
			//check file extension
		if(in_array($array[$key]->guessClientExtension(), $images)){
			return $this->processImage($array, $key);
		}else{
			return $this->processOtherFile($array, $key);
		}
	}

	public function processOtherFile($array, $key){
		$name = $this->processFilename($array, $key);
		$path = $array[$key]->storeAs("/public".$this->getUploadPath(), $name);
			// assign to the object
		return $path;
	}

	public function processImage($array, $key){
		$img  = Image::make($array[$key]);
		$height = $img->height();
		$width  = $img->height();

		if(array_key_exists($key."_height", $array)){
			$height = intval($array[$key."_height"]);
		}elseif(!array_key_exists($key."_width", $array)){
			$width = ($height / $img->height()) * $img->width();
		}

		if(array_key_exists($key."_width", $array)){
			$width = intval($array[$key."_width"]);
		}elseif(!array_key_exists($key."_height", $array)){
			$height = ($width / $img->width()) * $img->height();
		}

		if(array_key_exists($key."_operation", $array)){
			switch ($array[$key."_operation"]) {
				case 'resize':
				$img->resize($width, $height);
				break;
				case 'crop':
				$img->crop($width, $height);
				break;
				case 'fit':
				$img->fit($width, $height);
				break;
				default:
				echo('default');
				break;
			}
		}
			//type
			// $img->resize($width, $height);
		$type = $array[$key]->guessClientExtension();
		if(array_key_exists($key."_type", $array)){
			$type = $array[$key."_type"];
		}
			//quality
		$quality = 100;
		if(array_key_exists($key."_quality", $array)){
			$quality = $array[$key."_quality"];
		}
		$name = $this->processFilename($array, $key, $type);
			// save image
		$img  = Storage::put("public/uploads/".$name, $img->stream($type, $quality));
		return "public/uploads/".$name;
	}

	public function processFilename($array, $key, $type = null){
			// check if name already set up
		if(array_key_exists($key."_name", $array)){
			$name = $array[$key."_name"];
			$ext = strstr($name, ".");

				//if no extension detected in name
			if(!$ext){
				$ext = ".".$array[$key]->guessClientExtension();
				$name = $array[$key."_name"].$ext;
			}
				// if extension different with extension guesser
			if($ext != $type){
				$ext = ".".$type;
				$name = strstr($array[$key."_name"], ".", true).$ext;
			}
			return $name;
		}else{
			return md5($array[$key]).".".$type;
		}
	}
}
