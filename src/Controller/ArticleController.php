<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/all', name: 'app_article_all')]
    public function showArticle():Response{
        $article = [['titre'=>'nouveau film','contenu' =>'contenu du nouveau film','duree'=>120],['titre'=> 'Mario', 'contenu' => 'film animation', 'duree'=> 90]];     
        return $this->render('article/index2.html.twig', [
            'filmes'=> $article,
        ]);
    }
}
