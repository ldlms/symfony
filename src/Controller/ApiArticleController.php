<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class ApiArticleController extends AbstractController
{
    #[Route('/api/article/all',name:'app_api_article_all',methods:'GET')]
    public function getArticle(ArticleRepository $repo,):Response{

    $article = $repo->findAll();
    if(!$article){
        return $this->json(['erreur'=>'Il n\'y a pas d\'article'], 206, ['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> 'localhost',
        'Access-Control-Allow-Methods'=> 'GET']);
    }
    return $this->json($article,200,['Content-Type'=>'application/json',
    'Access-Control-Allow-Origin'=> 'localhost',
    'Access-Control-Allow-Methods'=>'GET'],['groups'=>'article:readAll']);
    }

    #[Route('/api/article/{id}',name:'app_api_article_id',methods:'GET')]
    public function getArticleById(ArticleRepository $repo,$id):Response{

    $article = $repo->find($id);
    if(!$article){
        return $this->json(['erreur'=>'Il n\'y a pas d\'article'], 206, ['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> 'localhost',
        'Access-Control-Allow-Methods'=> 'GET']);
    }
    return $this->json($article,200,['Content-Type'=>'application/json',
    'Access-Control-Allow-Origin'=> 'localhost',
    'Access-Control-Allow-Methods'=>'GET'],['groups'=>'article:readAll']);
    }

    #[Route('/api/article/add',name:'app_api_article_add',methods:'POST')]
    public function articleAdd(ArticleRepository $repo,Request $request, SerializerInterface $serialize, EntityManagerInterface $em):Response{
        $json = $request->getContent();
        $data = $serialize->decode($json,'json');
        
        $article = new Article();
        $cat = new Categorie();
        $user = new User();
        $article->setTitre($data['titre']);
        $article->setContenu($data['contenu']);
        $article->setDate(new DateTimeImmutable($data['date']));
        $cat->setNom($data['nom']);
        $user->setEmail($data['email']);

        $recup = $repo->findOneBy(['titre'=>$data['titre'],'date'=>$article->getDate()]);
        if($recup){
            return $this->json(['erreur'=>'L\'article existe déja'], 206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }

        $em->persist($article);
        $em->flush();

        return $this->json(['erreur'=>'L\'article '.$article->getTitre().' a été ajouté en BDD'],200,['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> 'localhost',
        'Access-Control-Allow-Methods'=> 'GET'],[]);

    }
}
