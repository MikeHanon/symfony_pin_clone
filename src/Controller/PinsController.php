<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;

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
        $pins = $repo->findAll();
        return $this->render('pins/index.html.twig', compact('pins'));
    }
    /**
     * @Route("/pins/create", name="pins_create", methods={"POST", "GET"})
     */
    public function create(Request $request, EntityManagerInterface $em) : Response
    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
        ->add('title', null, ['attr'=> ['autofocus'=>true]])
        ->add('description', null, ['attr'=>['rows'=>10, 'cols'=>50]])

            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($pin);
            $em->flush();

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
}
