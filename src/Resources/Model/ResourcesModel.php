<?php namespace Ordent\Ramenplatform\Resources\Model;
use Ordent\Ramenplatform\Contracts\Model\ResourcesModelInterface;
use Illuminate\Database\Eloquent\Model;
use Ordent\Ramenplatform\Resources\Traits\ResourcesModelTrait;
class ResourcesModel extends Model implements ResourcesModelInterface{

    use ResourcesModelTrait;
}
