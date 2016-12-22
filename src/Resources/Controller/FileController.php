<?php namespace Ordent\Ramenplatform\Resources\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller{

    public function get($path){
      return Storage::url($path);
    }
}
