<?php namespace Ordent\Ramenplatform\Resources\Response;
use Dingo\Api\Http\Response\Factory as DingoResponse;

class ResourceResponse extends DingoResponse{
    // factory function
    public function makeResponse($httpCode = 200, $message = null, $data = null, $transformer =null){

        if(substr($httpCode, 0, 1) == 2){
            return $this->successResponse($httpCode, $message, $data, $transformer);
        }

        return $this->errorResponse($httpCode, $message);

    }

    protected function successResponse($httpCode, $message, $data, $transformer){
        if($httpCode == 200){
            if(is_array($data)){
                return $this->array($data);
            }

            if($transformer == null){
                if($data instanceof \Illuminate\Support\Collection){
                    $transformer = $data->first()->getTransformer();
                }elseif($data instanceof \Illuminate\Contracts\Pagination\Paginator){
                    if(count($data->items)>0){
                        $transformer = $data->items()[0]->getTransformer();
                    }
                }else{
                    $transformer = $data->getTransformer();
                }
            }

            if($data instanceof \Illuminate\Support\Collection){
                return $this->collection($data, $transformer);
            }

            if($data instanceof \Illuminate\Contracts\Pagination\Paginator){
                return $this->paginator($data, $transformer);
            }

            return $this->item($data, $transformer);
        }

        if($httpCode == 201){
            return $this->created();
        }

        if($httpCode == 202){
            return $this->accepted();
        }

        if($httpCode == 204){
            return $this->noContent();
        }
    }

    protected function errorResponse($httpCode, $message){
        if($httpCode == 400){
            return $this->errorBadRequest($message);
        }

        if($httpCode == 401){
            return $this->errorUnauthorized($message);
        }

        if($httpCode == 403){
            return $this->errorForbidden($message);
        }

        if($httpCode == 404){
            return $this->errorNotFound($message);
        }

        if($httpCode == 405){
            return $this->errorMethodNotAllowed($message);
        }

        if($httpCode == 422){
            $response = app('Dingo\Api\Exception\ResourceException', ["validation failed", $message]);
            throw $response;
        }

        if($httpCode == 500){
            return $this->errorForbidden($message);
        }

        if($httpCode == 400){
            return $this->errorBadRequest($message);
        }


    }
}
