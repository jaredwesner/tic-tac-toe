<?php

namespace GameBundle\Service;

use GameBundle\Entity\User;

class UserService {

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
    
    public function login($email, $password) 
    {
        $result = null;
        
        try
        {
            $user = $this->em->getRepository('GameBundle:User')->findOneByEmail($email);
        }
        catch (\Exception $e)
        {
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            
            return $result;
        }

        if (!$user) 
        {
            try
            {
                $user = new User();
                
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
                $user->setHash(uniqid());
                $user->setCreatedAt(new \DateTime());

                $em = $this->em->getManager();

                $em->persist($user);

                $em->flush();
                
                $result = [
                    'success' => true,
                    'userHash' => $user->getHash()
                ];                
            }
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $result = [
                    'success' => false,
                    'message' => 'Email must be unique'
                ];
            }
            catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        else
        {
            if (password_verify($password, $user->getPassword()))
            {
                $result = [
                    'success' => true,
                    'userHash' => $user->getHash()
                ];
            }
            else
            {
                $result = [
                    'success' => false,
                    'message' => 'Wrong password' 
                ];
            }
        }

        return $result;
    }

}