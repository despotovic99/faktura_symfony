<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Entity\StavkaFakture;
use App\Form\FakturaType;
use App\Form\StavkaFaktureType;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fakture')]
class FakturaController extends AbstractController {

    #[Route('/', name: 'sve_fakture', methods: ['GET'])]
    public function index(ManagerRegistry $managerRegistry): Response {
        $fakture = $managerRegistry->getRepository(Faktura::class)->findAll();

        return $this->render('faktura/index.html.twig', [
            'fakture' => $fakture
        ]);
    }

    #[Route('/nova', name: 'nova_faktura', methods: ['GET'])]
    public function novaFaktura(ManagerRegistry $managerRegistry): Response {

        $faktura = new Faktura();
        $form = $this->napraviFormu($managerRegistry, $faktura);

        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{faktura}', name: 'prikazi_fakturu', methods: ['GET'])]
    public function radSaFakturom(Faktura $faktura, ManagerRegistry $managerRegistry) {
        $form = $this->napraviFormu($managerRegistry, $faktura);
        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sacuvaj', name: 'sacuvaj_fakturu', methods: ['POST'])]
    public function sacuvajFakturu(ManagerRegistry $managerRegistry, Request $request) {
        // todo pitaj kako da nazoves ovakve objekte s kojiima radis "privremeno" da li fakturaForma fakturaZahtev fakturaIzZahteva itd
        $fakturaObjekat = $request->get('form');
        $faktura = $managerRegistry->getRepository(Faktura::class)->find($fakturaObjekat['id']);

        if (!$faktura) {
            $faktura = new Faktura();
        }

        $entityManager = $managerRegistry->getManager();

        $managerRegistry->getConnection()->beginTransaction();
        try {
            $faktura->setBrojRacuna($fakturaObjekat['brojRacuna']);
            $faktura->setDatumIzdavanja(new \DateTime($fakturaObjekat['datumIzdavanja']));

            $organizacija = $entityManager->getRepository(Organizacija::class)->find($fakturaObjekat['organizacija']);

            $faktura->setOrganizacija($organizacija);

            $faktura->obrisiStavke();

            $entityManager->persist($faktura);
            $entityManager->flush();
            $managerRegistry->getConnection()->commit();
        } catch (\Throwable $e) {
            // todo dodaj  flash  ovde message
            $this->addFlash('poruka', 'Faktura nije sacuvana!');
            $managerRegistry->getConnection()->rollback();
            return $this->redirectToRoute('sve_fakture');
        }

        if (isset($fakturaObjekat['stavke'])) {
            foreach ($fakturaObjekat['stavke'] as $stavkaObjekat) {
                $stavka = $entityManager->getRepository(StavkaFakture::class)->find($stavkaObjekat['id']);
                if (!$stavka) {
                    $stavka = new StavkaFakture();
                }

                if ($stavka->getFaktura() != null && $stavka->getFaktura()->getId() != $faktura->getId()) {
                    // id fakture u stavci nije isti kao id fakture za kokju treba da se veze
                    // ne izbacujem poruku o gresci
                    continue;
                }

                $managerRegistry->getConnection()->beginTransaction();
                try {
                    $stavka->setNazivArtikla($stavkaObjekat['naziv_artikla']);
                    $stavka->setKolicina($stavkaObjekat['kolicina']);

                    $faktura->addStavke($stavka);

                    $entityManager->persist($faktura);
                    $entityManager->flush();
                    $managerRegistry->getConnection()->commit();
                } catch (\Throwable $e) {
                    $this->addFlash('poruka', "Problem prilikom cuvanja stavke " . $stavka->getNazivArtikla());
                    $managerRegistry->getConnection()->rollback();
                }
            }
        }
        $this->addFlash('poruka', "Uspesno sacuvana faktura");
        return $this->redirectToRoute('sve_fakture');
    }


    #[Route('/{faktura}', name: 'obrisi_fakturu', methods: ['DELETE'])]
    public function obrisiFakturu(Faktura $faktura, ManagerRegistry $managerRegistry) {

        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($faktura);
        $entityManager->flush();

        $this->addFlash('poruka', 'Uspesno obrisana faktura!');
        return $this->redirectToRoute('sve_fakture');
    }

    private function napraviFormu($managerRegistry, $faktura) {
        $organizacije = $managerRegistry->getRepository(Organizacija::class)->findAll();

        $form = $this->createFormBuilder($faktura)
            ->setAction($this->generateUrl('sacuvaj_fakturu'))
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->add('brojRacuna', TextType::class)
            ->add('datumIzdavanja', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('organizacija', ChoiceType::class, [
                'choices' => $organizacije,
                'choice_value' => 'id',
                'choice_label' => function (?Organizacija $organizacija) {
                    return $organizacija ? $organizacija->getNaziv() : '';
                }
            ])->add('stavke', CollectionType::class, [
                'entry_type' => StavkaFaktureType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'stavka-forma-class ']
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,

            ])->add('Sacuvaj_fakturu', SubmitType::class)
            ->getForm();
        return $form;
    }

}
