<?php
namespace Ordent\Ramenplatform\Resources\Request;
use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;

class ResourceRequestFactory{
    public static function createRequest(ResourceModelInterface $model, $key = null){
        $request =  app('Ordent\Ramenplatform\Resources\Request\ResourceRequest', [$model, $key]);
        return $request;
    }
}
