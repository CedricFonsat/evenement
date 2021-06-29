<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
   
        /**
     * @Route("/registration", name="registration")
     */
    public function index(ObjectManager $manager, Request $request, UserPasswordEncoderInterface  $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password =  $user->getPassword();
            $hash = $encoder->encodePassword($user, $password);
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('registration/index.html.twig', [
            "formulaire" => $form->createView()
        ]);
    }
}
