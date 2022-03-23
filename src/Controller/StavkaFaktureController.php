<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\StavkaFakture;
use App\Form\StavkaFaktureType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('stavka/fakture')]
class StavkaFaktureController extends AbstractController {
    #[Route('/nova/{faktura}', name: 'nova_stavka')]
    public function novaStavka(Faktura $faktura, Request $request, ManagerRegistry $managerRegistry): Response {

        if (!$faktura) {
            $this->redirectToRoute('faktura_forma');
        }

        $stavka = $request->get('stavka');

        if (!$stavka) {
            $stavka = new StavkaFakture();
        }
        $form = $this->createForm(StavkaFaktureType::class, $stavka);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faktura->removeStavke($stavka);
            $stavka = $form->getData();
            $faktura->addStavke($stavka);
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($faktura);
            $entityManager->persist($stavka);

            $entityManager->flush();

            $this->addFlash('poruka', "Uspesno sacuvana stavka");

            return $this->redirectToRoute('faktura',[
                'faktura'=>$faktura->getId()
            ]);

        }


        return $this->render('stavka_fakture/index.html.twig', [
            'form' => $form->createView(),
            'faktura' => $faktura
        ]);
    }

    #[Route('/{stavka}', name: 'rad_sa_stavkom')]
    public function radSaStavkom(StavkaFakture $stavka): Response {

        return $this->forward('App\Controller\StavkaFaktureController::novaStavka', [
            'faktura' => $stavka->getFaktura(),
            'stavka' => $stavka
        ]);

    }


    #[Route('/obrisi/{stavka}', name: 'obrisi_stavku')]
    public function obrisiStavku(StavkaFakture $stavka, ManagerRegistry $managerRegistry) {

        $faktura = $stavka->getFaktura();

        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($stavka);
        $entityManager->flush();

        $this->addFlash('poruka', 'Uspesno obrisana stavka');
        return $this->redirectToRoute('faktura', ['faktura' => $faktura->getId()]);


    }


}
