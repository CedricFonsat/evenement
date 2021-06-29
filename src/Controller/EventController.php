<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Category;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EventController extends AbstractController
{
    /**
     * @Route("/", name="event")
     */
    public function index(): Response
    {
        return $this->render('event/index.html.twig');
    }


    /**
     * @Route("/events", name="events")
     */ 
    public function showAll(EventRepository $repo)
    {
      $repo = $this->getDoctrine()->getRepository(Event::class); 
      $events = $repo->findAll();
      $picture = true;
      foreach ($events as $event) {
          if ($event->getImage() !== null || $event->getImage() !== "") {
              $picture = false;
          }else{
              
          }
      }



      return $this->render("event/showAll.html.twig",[
          "evenements" => $events,
          "picture" => $picture
      ]);
    }


    /**
     * @Route("/showone/{id}", name="showone")
     */ 
    public function showOne($id)
    {
    $repo = $this->getDoctrine()->getRepository(Event::class);
    $event = $repo->find($id);
    return $this->render("event/showone.html.twig",[
        "evenement" => $event
    ]);
    }


    /**
     * @Route("/createvent", name="createvent")
     * @Route("/editevent/{id}", name="editevent")
     */ 
    public function editEvent(Event $event =null,Request $request, ObjectManager $manager, SluggerInterface $slugger, UserInterface $user)
    {
         if(!$event){
             $event = new Event();
         }
         $form = $this->createForm(EventType::class,$event);
         $form->handleRequest($request);
         if($form->isSubmitted()&& $form->isValid()){
             //
            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                // $safeFilename = $slugger->slug($originalFilename);
                $newFilename = "imageEvent".'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setImage($newFilename);
            }
    //         return $this->redirectToRoute('');
    //     }

    //     return $this->render('', [
    //         'image' => $image->createView(),
    //     ]);
    // }}

             if(!$event){
                $event->setCreatedAt(new \DateTime());
             }

             $event->setUser($user);
             $manager->persist($event);
             $manager->flush();
             return $this->redirectToRoute("events");
         }
         $flag = false;
         // si le id de l'event != null alors le flag = true

         if($event->getId() !== null){   // getId() pour recuprere l'id avec les get et les set pour recup
             $flag = true;
         }

         return $this->render("event/edit.html.twig", [
             "form"=>$form->createView(),
             "flag"=>$flag
         ]);
    
    }

     /**
     * @Route("/remove/{id}", name="removeevent")
     */ 
     public function removeEvent(Event $event, ObjectManager $manager)
     {
        //  $manager = $this->getDoctrine()->getManager();
         $manager->remove($event);
         $manager->flush();
         return $this->redirectToRoute("events");
       
     }

     /**
      * @route("/afficheEventByUser", name="afficheEventByUser")
      */
      public function afficheEventByUser(UserInterface $user){
        $repo = $this->getDoctrine()->getRepository(User::class);
        $events = $repo->findEvenementByUser($user);
        return $this->render("event/showEventUser.html.twig", [
            "evenements"=>$events
        ]);
    }

}

