<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;

class ArticleController extends AbstractController
{

    #[Route('/article/all', name: 'app_article_all')]
    public function showAllArticle(ArticleRepository $articleRepository):Response{
        // $article = [['titre'=>'nouveau film','contenu' =>'contenu du nouveau film','duree'=>120],['titre'=> 'Mario', 'contenu' => 'film animation', 'duree'=> 90]];     
        //recuperer dans un tableau tous les articles, très pratique
        $articles = $articleRepository->findAll();
        // dd($articles);
        return $this->render('article/index2.html.twig', [
            'liste' => $articles
            // 'liste' => $articleRepository->findAll(); (marche aussi)
            // 'filmes'=> $article,
        ]);
    }

    #[Route('/article/id/{id}', name: 'app_article_id')]
    public function showArticleById(ArticleRepository $articleRepository, $id):Response{

        $update = "Update";

        $articles = $articleRepository->find($id);


        return $this->render('article/article.html.twig', [
            'article' => $articles,
            'update' => $update,
        ]);
        // recuperer l'article selon son id
        // faire un twig
    }

    #[Route('/article/add', name:'app_article_add')]
    public function addArticle(EntityManagerInterface $em, Request $request):Response{
        
        $msg = "";

        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if($form->isSubmitted() AND $form->isValid()){
            
            $em->persist($article);
            
            $em->flush();
            
            $msg = 'L\'article : '.$article->getId().' à été ajouté';
        }
    
        return $this->render('article/articleAdd.html.twig', [
            'form'=> $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/article/update/{id}', name:'app_article_update')]
    public function updateArticle(int $id, ArticleRepository $articleRepository, 
        EntityManagerInterface $em, Request $request):Response{
        $msg = "";

        $article = $articleRepository->find($id);

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()){

            $em->persist($article);

            $em->flush();

            $msg = 'L\'article : '.$article->getId().' à été modifié'; 
        }

        return $this->render('article/articleUpdate.html.twig', [
            'form'=> $form->createView(),
            'msg'=> $msg,
        ]);
    }
}
