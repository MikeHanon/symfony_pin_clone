<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{


    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }
    /**
     * @Route("/pins/create", name="pins_create", methods={"POST", "GET"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo) : Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);



        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success','Pin successfully created');
            return $this->redirectToRoute('app_home');
        }

       return $this->render('pins/create.html.twig', [
           'form'=> $form->createView()
       ]);
    }
    /**
     * @Route("/pins/{id<[0-9]+>}", name="pins_show", methods={"GET"})
     */
    public function show(PinRepository $repo, int $id): Response
    {
        $pin = $repo->find($id);
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="pins_edit", methods={"GET", "PUT"})
     */
    public function edit(PinRepository $repo, int $id, Request $request, EntityManagerInterface $em) : Response
    {
        $pin = $repo->find($id);
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash('success','Pin successfully updated');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form'=> $form->createView()
        ]);
    }
    /**
     * @Route("/pins/{id<[0-9]+>}", name="pins_delete", methods={"DELETE"})
     */
    public function delete(PinRepository $repo, int $id, Request $request, EntityManagerInterface $em) : Response
    {
        $pin = $repo->find($id);
        if($this->isCsrfTokenValid('pin_deletion_' . $id, $request->request->get('csrf_token'))){
        $em->remove($pin);
        $em->flush();
            $this->addFlash('info','Pin successfully deleted');
        }

        return $this->redirectToRoute('app_home');
    }
}
