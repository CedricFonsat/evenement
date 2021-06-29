<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    

    /**
     * @Route("/allcategory/",name="allcategory")
     */
    public function showAllCategory(){

    }

     /**
      * @route("/category/{id}", methods="GET", name="eventcategory")
      */
      public function afficheEventByCategory(CategoryRepository $repo, $id){
     
        $events = $repo->findEvenementByCategory($id);
        return $this->render("", [
         "category" => $events
        ]);
        // dd($events);

    }
}
