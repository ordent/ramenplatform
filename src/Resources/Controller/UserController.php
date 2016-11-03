<?php namespace Ordent\Ramenplatform\Resources\Controller;

use Ordent\Ramenplatform\Resources\Controller\ResourceController;
class UserController extends ResourceController{
    use ResourceControllerTrait;

    public function register(ResourceRequest $user = null){
        try{
            if(is_null($request) || $request->getModel() != $this->getModel()){
                $request = ResourceRequestFactory::createRequest($this->getModel(), 'store');
            }
        }catch(ValidationException $e){
            $response = $this->response->makeResponse(422, $e->validator->getMessageBag()->all());
        }
        $data = $request->except('password');
        $data['password'] = hash('sha1', $request->password);

        $request->replace($data);

        return $this->store($request);
    }

    public function login(){

    }
}
