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
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Serializer\SerializerInterface;


class ApiArticleController extends AbstractController
{
    #[Route('/api/article/all',name:'app_api_article_all',methods:'GET')]
    public function getArticle(ArticleRepository $repo,):Response{

        $article = $repo->findAll();
        if(!$article){
            return $this->json(['erreur'=>'Il n\'y a pas d\'article'], 206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }
        return $this->json($article,200,['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> '*',
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
    public function articleAdd(ArticleRepository $repoA, CategorieRepository $repoC, UserRepository $repoU, Request $request, SerializerInterface $serialize, EntityManagerInterface $em):Response{
        $json = $request->getContent();
        $data = $serialize->decode($json,'json');
        dd($data);
        $user = $repoU->findOneBy(['email'=>$data['user']['email']]);
        // penser a verifier que le json n'est pas vide
        $article = new Article();
        $article->setTitre($data['titre']);
        $article->setContenu($data['contenu']);
        $article->setDate(new \DateTimeImmutable($data['date']));
        $article->setUser($user);
        
        foreach($data['categories'] as $cat){
            $catego = $repoC->findOneBy(['nom'=>$cat['nom']]);
            $article->addCategorie($catego);
        }
        

        $recup = $repoA->findOneBy(['titre'=>$data['titre'],'date'=>$article->getDate()]);
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

    #[Route('/api/article/delete',name:'app_api_article_delete',methods:'DELETE')]
    public function articleDelete(Request $request,SerializerInterface $serialize,EntityManagerInterface $em, ArticleRepository $repo):Response{

        $json = $request->getContent();
        $data = $serialize->decode($json,'json');

        $article = $repo->findOneBy(['id'=>$data['id']]);

        if(!$article){
            return $this->json(['erreur'=>'cet article n\'existe pas'], 206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }

        $em->remove($article);
        $em->flush();
        // ON ARRIVE ICI A ACC2DER AU TITRE DE ARTICLE? MALGRES LE FAIT QUIL SOIT FLUSH? CAR LA VARIABLE EXISTE ENCORE
        return $this->json(['erreur'=>'L\'article '.$article->getTitre().' a été bien suprimé'],200,['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> '*',
        'Access-Control-Allow-Methods'=> 'GET'],[]);

    }
}
