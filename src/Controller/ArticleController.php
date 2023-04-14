<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{

    #[Route('/article/all', name: 'app_article_all')]
    public function showArticle(ArticleRepository $articleRepository):Response{
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

        $articles = $articleRepository->find($id);


        return $this->render('article/index.html.twig', [
            'article' => $articles,
        ]);
        // recuperer l'article selon son id
        // faire un twig
    }

    #[Route('/article/add', name:'app_article_add')]
    public function addArticle():Response{
        
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
    
        return $this->render('article/articleAdd.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
