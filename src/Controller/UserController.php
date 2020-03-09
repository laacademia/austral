<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
	/**
	* @Route("/", name="api_home")
	*/
	public function home()
    {
       return $this->json(['result' => true]);
    }
	/**
	* @Route("/profile", name="api_profile")
	* @IsGranted("ROLE_USER")
	*/
    public function profile()
    {
       return $this->json([
           'user' => $this->getUser()
       ]);
    }
	/**
	* @Route("/login", name="api_login", methods={"POST"})
	*/
    public function login()
    {
             return $this->json([
		          'user' => $this->getUser()
		       ], 
		       200, 
		       [], 
		       [
		          'groups' => ['api']
		       ]
		    );  
    }

	/**
	* @Route("/register", name="api_register", methods={"POST"})
	*/
    public function register(EntityManagerInterface $om, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
    	$user = new User();

    	$email                  = $request->get("email");
		$password               = $request->get("password");
		$passwordConfirmation   = $request->get("password_confirmation");

		$errors = [];
	    if($password != $passwordConfirmation)
	    {
	         $errors[] = "Password does not match the password confirmation.";
	    }
        if(strlen($password) < 6)
	    {
	       $errors[] = "Password should be at least 6 characters. Lenght:".strlen($password);
	    }

        if(!$errors)
	    {
			try
			{
				$encodedPassword = $passwordEncoder->encodePassword($user, $password);
				$user->setEmail($email);
				$user->setPassword($encodedPassword);

				$om->persist($user);
				$om->flush();
				return $this->json([
				   'user' => $user
				]);
			}
			catch(UniqueConstraintViolationException $e)
			{
			   $errors[] = "The email provided already has an account!";
			}
			catch(\Exception $e)
			{
			   $errors[] = "Unable to save new user at this time.". $e->getMessage();
			}
	    }

		return $this->json([
		   'errors' => $errors		  
		], 400);
    }
}
