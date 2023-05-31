<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\ApiRegister;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\SerializerInterface;

  class ApiController extends AbstractController
  { 
    #[Route('/api/verif', name: 'app_api_verif')]   
    public function verifConnexion(Request $request, UserPasswordHasherInterface $hash,ApiRegister $reg,UserRepository $repo){
        $mail =$request->query->get('email');
        $mdp = $request->query->get('password');
        $verif = $reg->authentification($hash,$repo,$mail,$mdp);
        if($verif){
            return $this->json(['connexion'=>'ok'],200,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }else{
            return $this->json(['connexion'=>'invalide'],400,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }
    }

    #[Route('/api/register', name:'app_api_register')]
    public function getToken(Request $request, UserRepository $repo,
        UserPasswordHasherInterface $hash, ApiRegister $apiRegister,
        SerializerInterface $serialize){
        //récupérer le json
        $json = $request->getContent();
        //test si on n'à pas de json
        if(!$json){
            //renvoyer un json
            return $this->json(['erreur'=>'Le Json est vide ou n\'existe pas'], 400, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
        //transformer le json en tableau
        $data = $serialize->decode($json, 'json');
       
        //récupération du mail et du password
        $mail = $data['email'];
        $password = $data['password']; 

        //test si le paramétre mail n'est pas saisi
        if(!$mail OR !$password){
            return $this->json(['Error'=>'informations absentes'], 400,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*'] );
        }
        //test si le compte est authentifié
        if($apiRegister->authentification($hash,$repo,$mail,$password)){
            //récupération de la clé de chiffrement
            $secretKey = $this->getParameter('token');
            //génération du token
            $token = $apiRegister->genToken($mail, $secretKey, $repo);
            //Retourne le JWT
            return $this->json(['Token_JWT'=>$token], 200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        //test si le compte n'est pas authentifié (erreur mail ou password)
        else{
            return $this->json(['Error'=>'Informations de connexion incorrectes'], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
    }
    #[Route('/api/testToken', name:'app_api_testToken')]
    public function testToken(Request $request, ApiRegister $reg){
        $token = $request->server->get('HTTP_AUTHORIZATION');
        $newToken = substr($token,7);
        $key =$this->getParameter('token');
        $verified = $reg->verifyToken($newToken, $key);
        if($verified === true){
            return $this->json(['authentification'=>'ok'],200,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }else{
            return $this->json(['connexion'=>'invalide'],400,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Methods'=> 'GET']);
        }
    }

    #[Route('api/localToken', name:'app_api_local_token')]
    public function localToken():Response{
        return $this->render('api/local.html.twig');
    }
  }
?>