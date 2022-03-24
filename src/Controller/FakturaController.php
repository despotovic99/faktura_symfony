<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Entity\StavkaFakture;
use App\Form\FakturaType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fakture')]
class FakturaController extends AbstractController {

    #[Route('/', name: 'sve_fakture',methods: ['GET'])]
    public function index(ManagerRegistry $managerRegistry): Response {
        $fakture = $managerRegistry->getRepository(Faktura::class)->findAll();

        return $this->render('faktura/index.html.twig', [
            'fakture' => $fakture
        ]);
    }



    #[Route('/nova', name: 'nova_faktura', methods: ['GET','POST'])]
    public function novaFaktura(?Faktura $faktura,ManagerRegistry $managerRegistry, Request $request,SessionInterface $session): Response {

        $organizacije = $managerRegistry->getRepository(Organizacija::class)->findAll();

//        $faktura = $session->get('faktura');
//&& !$session->get('faktura'
        if (!$faktura ) {
            $faktura = new Faktura();
        }


        $form = $this->createFormBuilder($faktura)
            ->add('broj_racuna', TextType::class)
            ->add('datum_izdavanja', DateType::class, [
                'widget' => 'single_text'
            ])
            //  pitaj kako da ovo uradis na nacin da izolujes formu
            ->add('organizacija', ChoiceType::class, [
                'choices' => $organizacije,
                'choice_value' => 'id',
                'choice_label' => function (?Organizacija $organizacija) {
                    return $organizacija ? $organizacija->getNaziv() : '';
                }
            ])
            ->add('sacuvaj', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $faktura=$form->getData();
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($faktura);

            $entityManager->flush();

            $this->addFlash('poruka',"Uspesno sacuvana faktura");

        }

        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
            'faktura'=>$faktura,
        ]);

    }

    #[Route('/{faktura}', name: 'prikazi_fakturu')]
    public function radSaFakturom(Faktura $faktura) {
        return $this->forward('App\Controller\FakturaController::novaFaktura', [
            'faktura' => $faktura
        ]);
    }

    #[Route('/{faktura}/obrisi', name: 'obrisi_fakturu')]
    public function obrisiFakturu(Faktura $faktura,ManagerRegistry $managerRegistry) {

        $entityManager =  $managerRegistry->getManager();
        $entityManager->remove($faktura);
        $entityManager->flush();

        $this->addFlash('poruka','Uspesno obrisana faktura!');
        return $this->redirectToRoute('sve_fakture');
    }



}
