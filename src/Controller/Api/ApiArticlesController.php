<?php
namespace App\Controller\Api;

use App\Repository\ArticleRepository;
use symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use symfony\component\Routing\Annotation\Route;
use symfony\component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiRegister;
use App\Entity\Article;
use Symfony\Component\Serializer\SerializerInterface;


Class ApiArticlesController extends AbstractController{


    #[Route('api/articles/get/id/{id}', name:'app_api_articles_all')]
    public function getArticle(Request $request,ArticleRepository $repo, ApiRegister $api,SerializerInterface $serialize,$id):Response{
        //vérifier le token 
            //récupérer la clé
            $secretKey = $this->getParameter('token');
            //récupérer le token 
            $jwt = substr($request->server->get('HTTP_AUTHORIZATION'),7);
            //test si le token n'existe pas
            if($jwt==''){
                return $this->json(['Error'=>'le token n\'existe pas'], 400, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*']);
            }
            $verif = $api->verifyToken($jwt, $secretKey);
            //si valide
            if($verif===true){
                $json = $request->getContent();
                $id = $serialize->decode($json,'json');
                $article = $repo->find($id);
                if($article){
                    return $this->json($article, 200, ['Content-Type'=>'application/json',
                    'Access-Control-Allow-Origin'=> '*'], ['groups'=>'article:readAll']);
                }
            }else{
                return $this->json(['Error'=>$verif], 400, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*']);
            }
    }

    #[Route('/api/articles/get/all', name:'app_api_articles_all')]
    public function getAllArticles(Request $request,ArticleRepository $repo, ApiRegister $api):Response{

            //vérifier le token 
            //récupérer la clé
            $secretKey = $this->getParameter('token');
            //récupérer le token 
            $jwt = substr($request->server->get('HTTP_AUTHORIZATION'),7);
            //test si le token n'existe pas
            if($jwt==''){
                return $this->json(['Error'=>'le token n\'existe pas'], 400, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*']);
            }
            $verif = $api->verifyToken($jwt, $secretKey);
            //si valide
            if($verif===true){
                //récupérer la liste des articles
                $data = $repo->findAll();
                //tester si on à bien des articles
                if($data){
                    return $this->json($data, 200, ['Content-Type'=>'application/json',
                    'Access-Control-Allow-Origin'=> '*'], ['groups'=>'article:readAll']);
                }
                else{
                    return $this->json(['Error'=>'Pas d\'articles en BDD'], 206, ['Content-Type'=>'application/json',
                    'Access-Control-Allow-Origin'=> '*']);
                }
            }
            //sinon on envoyer un json d'erreur (pas authorisé)
            else{
                return $this->json(['Error'=>$verif], 400, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*']);
            }  
        }

}

?>