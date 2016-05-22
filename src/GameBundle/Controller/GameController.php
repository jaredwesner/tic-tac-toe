<?php

namespace GameBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;

class GameController extends FOSRestController
{
    /**
     * @Post("/games/start/{type}/{mode}")
     */
    public function gameStartAction($type, $mode)
    {
        $request = Request::createFromGlobals();
        $userHash = $request->headers->get('Authorization');
        
        $view = null;
        
        $body = json_decode($request->getContent());
        $service = $this->get('game.service');
        
        $result = $service->start($userHash, $type, $mode);
        
        if ($result['success'])
        {
            $view = $this->view([
                'gameHash' => $result['gameHash']
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
    
    /**
     * @Put("/games/{gameHash}")
     */
    public function gamePlayAction($gameHash)
    {
        $request = Request::createFromGlobals();
        $userHash = $request->headers->get('Authorization');
        
        $view = null;
        
        $body = json_decode($request->getContent());
        $service = $this->get('game.service');
        
        $result = $service->play($userHash, $gameHash, $body->column, $body->row);
        
        if ($result['success'])
        {
            $tmp = array();
            $tmp['gameplay'] = json_decode($result['gameplay']);
            if (array_key_exists('winner', $result))
                $tmp['winner'] = $result['winner'];
            $view = $this->view($tmp, 200);
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
