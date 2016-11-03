<?php namespace Ordent\Ramenplatform\Contracts\Model;

interface ResourceModelInterface{
    public function setTransformer($transformer);

    public function getTransformer();

    public function setRules($key, Array $attributes, $appends = false);

    public function getRules($key);

    public function setUploadPath($path);

    public function getUploadPath();

    public function hasSlug();

    public function getTransformedData();

    public function setIndexFilters($scope, $appends = false);

    public function getIndexFilters();
}
