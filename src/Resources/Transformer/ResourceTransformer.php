<?php namespace Ordent\Ramenplatform\Resources\Transformer;
use League\Fractal\TransformerAbstract;
use Ordent\Ramenplatform\Contracts\Model\ResourceModelInterface;

class ResourceTransformer extends TransformerAbstract{
    //passing contracts so model can pass as long as it implement the contracts
    public function transform(ResourceModelInterface $model){
        dd($model->getTransformedData());
        return $model->getTransformedData();
    }
}
