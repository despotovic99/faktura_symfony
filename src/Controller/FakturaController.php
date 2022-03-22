<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/faktura')]
class FakturaController extends AbstractController {
    #[Route('/', name: 'sve_fakture')]
    public function index(ManagerRegistry $managerRegistry): Response {
        $fakture = $managerRegistry->getRepository(Faktura::class)->findAll();

        return $this->render('faktura/index.html.twig', [
            'fakture' => $fakture
        ]);
    }

    #[Route('/form', name: 'faktura_forma')]
    public function fakturaForma(?Faktura $faktura, ManagerRegistry $managerRegistry, Request $request): Response {

        $organizacije = $managerRegistry->getRepository(Organizacija::class)->findAll();

        if (!$faktura) {
            $faktura = new Faktura();
        }

        $form = $this->createFormBuilder($faktura)
            ->add('broj_racuna', TextType::class)
            ->add('datum_izdavanja', DateType::class, [
                'widget' => 'single_text'
            ])
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
            'faktura'=>$faktura
        ]);

    }

    #[Route('/{id}', name: 'faktura',methods: ['GET','POST'])]
    public function radSaFakturom(Faktura $faktura) {

        return $this->forward('App\Controller\FakturaController::fakturaForma', [
            'faktura' => $faktura
        ]);
    }

    #[Route('/{id}', name: 'obrisi_fakturu',methods: ['DELETE'])]
    public function obrisiFakturu(Faktura $faktura,ManagerRegistry $managerRegistry) {

        $entityManager =  $managerRegistry->getManager();
        $entityManager->remove($faktura);
        $entityManager->flush();

        $this->addFlash('poruka','Uspesno obrisana faktura!');
        return $this->redirectToRoute('sve_fakture');
    }



}
