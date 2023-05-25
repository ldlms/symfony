<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContactRepository;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }

    #[Route('/contact/add', name:'app_contact_add')]
    public function addContact(EntityManagerInterface $em, Request $request, ContactRepository $repo):Response{
        
        $msg = "";

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        
        if($form->isSubmitted() AND $form->isValid()){

            $recup = $repo->findOneBy(['nom'=>$contact->getNom()]);
            
            if(!$recup){

            $em->persist($contact);
            
            $em->flush();
            
            $msg = 'Le contact : '.$contact->getId().' à été ajouté';
            }else{
                $msg = 'Le contact existe déja';
            }
        }
    
        return $this->render('contact/contactAdd.html.twig', [
            'form'=> $form->createView(),
            'msg' => $msg,
        ]);
    }
}
