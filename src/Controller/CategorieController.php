<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Service\Utils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    #[Route('/categorie/add', name:'app_categorie_add')]
    public function addCategorie(EntityManagerInterface $em, Request $request):Response{

        $msg = "";
        
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        
        if($form->isSubmitted() AND $form->isValid()){
            
            $em->persist($categorie);
            
            $em->flush();
            
            $msg = 'La categorie : '.$categorie->getNom().' à été ajouté';
        }
    
        return $this->render('categorie/categorieAdd.html.twig', [
            'form'=> $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/categorie/all', name:'app_categorie_all')]
    public function showAllCategorie(CategorieRepository $categorieRepository):Response{

        $categorie = $categorieRepository->findAll();
        
        return $this->render('categorie/categorie.html.twig', [
            'liste' => $categorie
        ]);
    }

    #[Route('/categorie/update/{id}', name:'app_categorie_update')]
    public function updateCategorie(int $id, CategorieRepository $categorieRepository, 
        EntityManagerInterface $em, Request $request):Response{
        $msg = "";

        $categorie = $categorieRepository->find($id);

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()){

            $em->persist($categorie);

            $em->flush();

            $msg = 'La categorie : '.$categorie->getId().' à été modifié'; 
        }

        return $this->render('categorie/categorieUpdate.html.twig', [
            'form'=> $form->createView(),
            'msg'=> $msg,
        ]);
    }

    #[route('/categorie/delete/{id}', name:'app_categorie_delete')]
    public function deleteCategorie(int $id, CategorieRepository $categorieRepository, EntityManagerInterface $em, Request $request): Response{

        $categorie = $categorieRepository->find($id);
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('app_categorie_all', [

        ]);

    }
}
