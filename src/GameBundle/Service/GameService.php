<?php

namespace GameBundle\Service;

use GameBundle\Entity\Game;

class GameService {
    
    CONST BLANK_SPACE = '_';

    protected $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    public function start($userHash, $type, $mode)
    {
        $validation = array();
        
        if (!$userHash)
            $validation[] = 'Authentication Header is required';
        
        if ($type != 1 && $type != 2)
            $validation[] = 'Game Type must be 1 (Regular Tic Tac Toe) ou 2 (Ultimate Tic Tac Toe)';
        
        if ($mode != 1)
            $validation[] = 'Game Mode must be 1 (Versus COM)';
        
        $result = null;
        
        if (count($validation))
        {
            $result = [
                'success' => false,
                'message' => $validation
            ];
            
            return $result;
        }
        
        try
        {
            $user = $this->em->getRepository('GameBundle:User')->findOneByHash($userHash);
            
            if (!$user)
                throw new \Exception('User not found for hash '.$userHash);
            
            $game = new Game();
            
            $game->setUser($user->getId());
            $game->setType($type);
            $game->setMode($mode);
            $game->setHash(uniqid());
            $game->setCreatedAt(new \DateTime());
            $game->setGamePlay($this->generateGamePlay($game));

            $em = $this->em->getManager();

            $em->persist($game);

            $em->flush();
            
            $result = [
                'success' => true,
                'gameHash' => $game->getHash()
            ];
        }
        catch (\Exception $e)
        {
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    public function play($userHash, $gameHash, $column, $row, $ultimateColumn = null, $ultimateRow = null)
    {
        $validation = array();
        
        if (!$userHash)
            $validation[] = 'Authentication Header is required';
        
        if ($column < 1 || $column > 3)
            $validation[] = 'Column must be a valid integer between 1 and 3';
        
        if ($row < 1 || $row > 3)
            $validation[] = 'Row must be a valid integer between 1 and 3';
        
        $result = null;
        
        if (count($validation))
        {
            $result = [
                'success' => false,
                'message' => $validation
            ];
            
            return $result;
        }
        
        try
        {
            $user = $this->em->getRepository('GameBundle:User')->findOneByHash($userHash);
            
            if (!$user)
                throw new \Exception('User not found for hash '.$userHash);
                
            $game = $this->em->getRepository('GameBundle:Game')->findOneByHash($gameHash);
            
            if (!$game)
                throw new \Exception('Game not found for hash '.$gameHash);
                
            if ($game->getFinished() || $game->getAbandoned())
                throw new \Exception('This game has already finished. Try start a new one');
                
            $game->setGamePlay($this->generateGamePlay($game, true, $column, $row));
            $this->checkGameOver($game);
            
            if (!$game->getFinished())
            {
                if ($game->getMode() == 1)
                {
                    $values = $this->getComputerMove($game);
                    $game->setGamePlay($this->generateGamePlay($game, false, $values['col'], $values['row']));
                    $this->checkGameOver($game);
                }
            }
            
            $em = $this->em->getManager();

            $em->persist($game);

            $em->flush();
            
            if ($game->getFinished())
            {
                $result = [
                    'success' => true,
                    'gameplay' => $game->getGamePlay(),
                    'winner' => $game->getWinner().' WINS!!!'
                ];
            }
            else
            {
                $result = [
                    'success' => true,
                    'gameplay' => $game->getGamePlay()
                ];
            }
        }
        catch (\Exception $e)
        {
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    public function giveup($userHash, $gameHash)
    {
        
    }
    
    private function generateGamePlay($game, $x = true, $column = null, $row = null, $ultimateColumn = null, $ultimateRow = null)
    {
        $gameplay = null;
        
        if (!$game->getGamePlay())
        {
            if ($game->getType() == 1) 
            {
                $gameplay = array(
                    array(BLANK_SPACE, BLANK_SPACE, BLANK_SPACE),
                    array(BLANK_SPACE, BLANK_SPACE, BLANK_SPACE),
                    array(BLANK_SPACE, BLANK_SPACE, BLANK_SPACE)
                );
            }
            else
            {
                //TODO: 
            }
        }
        else
        {
            $gameplay = json_decode($game->getGamePlay());
            
            if ($gameplay[$row-1][$column-1] != BLANK_SPACE)
                throw new \Exception('Invalid movement. Please select another column and row');
                
            $gameplay[$row-1][$column-1] = $x ? 'X' : 'O';
        }
        
        return json_encode($gameplay);
    }
    
    private function checkGameOver($game)
    {
        $gameplay = json_decode($game->getGamePlay());
        
        // [0][0] | [0][1] | [0][2]
        // [1][0] | [1][1] | [1][2]
        // [2][0] | [2][1] | [2][2]
        
        // Regular Tic Tac Toe
        if ($game->getType() == 1) 
        {
            $winner = null;
            $hasEmptyCel = false;
            //Rows
            for ($i = 0; $i < 3; $i++)
            {
                if (!in_array(BLANK_SPACE, $gameplay[$i]) )
                {
                    if ($gameplay[$i][0] == $gameplay[$i][1] && $gameplay[$i][1] == $gameplay[$i][2])
                    {
                        $winner = $gameplay[$i][0];
                        break;
                    }
                }
                else
                {
                    $hasEmptyCel = true;
                }
            }
            
            //Columns
            if (!$winner)
            {
                for ($i = 0; $i < 3; $i++)
                {
                    if (!in_array(BLANK_SPACE, array($gameplay[0][$i], $gameplay[1][$i], $gameplay[2][$i]))
                        && $gameplay[0][$i] == $gameplay[1][$i] && $gameplay[1][$i] == $gameplay[2][$i])
                    {
                        $winner = $gameplay[0][$i];
                        break;
                    }
                }
            }
            
            //Diagonal 1
            if (!$winner)
            {
                if (!in_array(BLANK_SPACE, array($gameplay[0][0], $gameplay[1][1], $gameplay[2][2]))
                    && $gameplay[0][0] == $gameplay[1][1] && $gameplay[1][1] == $gameplay[2][2])
                {
                    $winner = $gameplay[0][0];
                }
            }
            
            //Diagonal 2
            if (!$winner)
            {
                if (!in_array(BLANK_SPACE, array($gameplay[0][2], $gameplay[1][1], $gameplay[2][0]))
                    && $gameplay[0][2] == $gameplay[1][1] && $gameplay[1][1] == $gameplay[2][0])
                {
                    $winner = $gameplay[0][2];
                }
            }
            
            if ($winner || !$hasEmptyCel)
            {
                if ($winner)
                    $game->setWinner($winner == 'X' ? 'PLAYER' : 'COM');
                else
                    $game->setWinner('TIE');
                $game->setFinished(true);
            }
        }
        else
        {
            
        }
    }
    
    public function getComputerMove($game)
    {
        $gameplay = json_decode($game->getGamePlay());
        
        // First, Is it possible to win with this movement?
        
        // If not to win then must get in users way
        
        for ($col = 0; $col < 3; $col++)
        {
            for ($row = 0; $row < 3; $row++)
            {
                if ($gameplay[$row][$col] == BLANK_SPACE)
                {
                    return array('col' => $col + 1, 'row' => $row + 1);
                }
            }
        }
    }
}