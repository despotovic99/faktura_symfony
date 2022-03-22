<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Form\FakturaType;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
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
    public function fakturaForma(?Faktura $faktura, ManagerRegistry $managerRegistry): Response {

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
            ->getForm();

        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/{id}', name: 'prikazi_fakturu')]
    public function prikaziFakturu(Faktura $faktura) {

        return $this->forward('App\Controller\FakturaController::fakturaForma', [
            'faktura' => $faktura
        ]);

    }

}
