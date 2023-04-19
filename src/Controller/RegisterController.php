<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;
use App\Entity\User;
use App\Form\CategorieType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function userAdd(EntityManagerInterface $em,UserRepository $repo,Request $request, UserPasswordHasherInterface $hash): Response
    {
        $msg = '';

        $user = new User();

        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);

        $recup = $repo->findOneBy(['email'=>$user->getEmail()]);

        if($form->isSubmitted() AND $form->isValid()){
        if($recup){
            $msg = "Le compte : ".$user->getEmail()." existe déja";
        }else{
            $pass = Utils::cleanInputStatic($request->request->all('user')['password']['first']);
            $hash = $hash->hashPassword($user,$pass);
            $nom = Utils::cleanInputStatic($request->request->all('user')['nom']);
            $prenom = Utils::cleanInputStatic($request->request->all('user')['prenom']);
            $email = Utils::cleanInputStatic($request->request->all('user')['email']);

            $user->setPassword($hash);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            $msg = "Le compte : ".$user->getEmail()." a été ajouté en BDD";
        }
        }
        return $this->render('register/index.html.twig', [
            'form'=>$form->createview(),
            'msg'=>$msg,
        ]);
    }
}
