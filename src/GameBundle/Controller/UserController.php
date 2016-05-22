<?php

namespace GameBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    public function postUserAction()
    {
        $request = Request::createFromGlobals();
        $body = json_decode($request->getContent());
        $service = $this->get('user.service');
        
        $result = $service->login($body->email, $body->password);        
        $view = null;
        
        if ($result['success'])
        {
            $view = $this->view([
                'userHash' => $result['userHash']
            ], 200);
        }
        else
        {
            $view = $this->view([
                'error' => $result['message']
            ], 400);
        }
        
        return $this->handleView($view);
    }
}
