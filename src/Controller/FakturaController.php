<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Form\FakturaType;
use App\Form\StavkaFaktureType;
use App\Services\FakturaDatabaseService;
use App\Services\Stampanje\ExcelFakturaStampanje;
use App\Services\Stampanje\FakturaStampanje;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fakture')]
class FakturaController extends AbstractController {

    private FakturaDatabaseService $fakturaController;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->fakturaController = new FakturaDatabaseService($managerRegistry);
    }

    #[Route('/', name: 'sve_fakture', methods: ['GET'])]
    public function index(): Response {
        $fakture = $this->fakturaController->findAll();

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

    #[Route('/stampanje', name: 'stampanje', methods: ['POST'])]
    public function stampanje(Request $request,ManagerRegistry $managerRegistry):Response {

        $faktura = $managerRegistry->getRepository(Faktura::class)->find($request->get('faktura-id-stampanje'));

        if(!$faktura){
            $this->addFlash('poruka','Problem sa stampanjem fakture');
            return $this->index();
        }

        try{
            $stampac = new FakturaStampanje($request->get('format-dokumenta'));
            $file = $stampac->stampaj($faktura);
            $putanja=$this->getParameter('download_directory');
            return $this->file($putanja.'/'.$file);
        }catch (\Throwable $exception){
            $this->addFlash('poruka','Problem sa stampanjem fakture');
            return $this->index();
        }

    }

    #[Route('/{faktura}', name: 'prikazi_fakturu', methods: ['GET'])]
    public function radSaFakturom(Faktura $faktura, ManagerRegistry $managerRegistry) {
        // todo param converterer - ?zameniti metodom pronadji fakturu?
        $form = $this->napraviFormu($managerRegistry, $faktura);
        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
            'faktura'=>$faktura
        ]);
    }

    #[Route('/sacuvaj', name: 'sacuvaj_fakturu', methods: ['POST'])]
    public function sacuvajFakturu(Request $request) {
        $fakturaObjekat = $request->get('form');

        $poruka = $this->fakturaController->save($fakturaObjekat);

        $this->addFlash('poruka', $poruka);
        return $this->redirectToRoute('sve_fakture');
    }


    #[Route('/{faktura}', name: 'obrisi_fakturu', methods: ['DELETE'])]
    public function obrisiFakturu(Faktura $faktura) {
        $poruka = 'Uspesno obrisana faktura!';

        if (!$this->fakturaController->delete($faktura)) {
            $poruka = 'Problem prilikom brisanja fakture!';
        }
        $this->addFlash('poruka', $poruka);
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
