<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Form\FakturaType;
use App\Form\StavkaFaktureType;
use App\Services\FakturaDatabaseService;
use App\Services\OrganizacijaDatabaseService;
use App\Services\Stampanje\ExcelFakturaStampanje;
use App\Services\Stampanje\FakturaStampanjeServis;
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

    private FakturaDatabaseService $fakturaDBServis;
    private FakturaStampanjeServis $fakturaStampanjeServis;
    private OrganizacijaDatabaseService $organizacijaDBServis;

    public function __construct(FakturaDatabaseService $fakturaDBServis, FakturaStampanjeServis $fakturaStampanjeServis, OrganizacijaDatabaseService $organizacijaDBServis) {
        $this->fakturaDBServis = $fakturaDBServis;
        $this->fakturaStampanjeServis = $fakturaStampanjeServis;
        $this->organizacijaDBServis = $organizacijaDBServis;
    }

    #[Route('/', name: 'sve_fakture', methods: ['GET'])]
    public function index(): Response {
        $fakture = $this->fakturaDBServis->findAll();

        return $this->render('faktura/index.html.twig', [
            'fakture' => $fakture
        ]);
    }

    #[Route('/nova', name: 'nova_faktura', methods: ['GET'])]
    public function novaFaktura(): Response {

        $organizacije = $this->organizacijaDBServis->findAll();

        $form = $this->napraviFormu($organizacije,null);

        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/stampanje', name: 'stampanje', methods: ['POST'])]
    public function stampanje(Request $request) {

        $idFakture = $request->get('faktura-id-stampanje');
        $formatStampe = $request->get('format-dokumenta');

        $file = $this->fakturaStampanjeServis->stampaj($idFakture, $formatStampe);

        if ($file === false) {
            $this->addFlash('poruka', 'Problem sa stampanjem fakture');
            return $this->redirectToRoute('prikazi_fakturu',['faktura'=>$idFakture]);
        }
        $putanja = $this->getParameter('download_directory');

        return $this->file($putanja . '/' . $file);
    }

    #[Route('/{faktura}', name: 'prikazi_fakturu', methods: ['GET'])]
    public function radSaFakturom(Faktura $faktura) {

        // mozda korisnik ne sme da vidi fakturu
        $faktura=$this->fakturaDBServis->find($faktura->getId());

        $organizacije=$this->organizacijaDBServis->findAll();
        $form = $this->napraviFormu($organizacije, $faktura);

        return $this->render('faktura/faktura.html.twig', [
            'form' => $form->createView(),
            'faktura' => $faktura
        ]);
    }

    #[Route('/sacuvaj', name: 'sacuvaj_fakturu', methods: ['POST'])]
    public function sacuvajFakturu(Request $request) {
        $fakturaSaForme = $request->get('form');

        $poruka = $this->fakturaDBServis->save($fakturaSaForme);

        $this->addFlash('poruka', $poruka);
        return $this->redirectToRoute('sve_fakture');
    }


    #[Route('/{faktura}', name: 'obrisi_fakturu', methods: ['DELETE'])]
    public function obrisiFakturu(Faktura $faktura) {
        $poruka = 'Uspesno obrisana faktura!';

        $result = $this->fakturaDBServis->delete($faktura);
        if (!$result) {
            $poruka = 'Problem prilikom brisanja fakture!';
        }
        $this->addFlash('poruka', $poruka);
        return $this->redirectToRoute('sve_fakture');
    }


    private function napraviFormu($organizacije, ?Faktura $faktura) {

        if (!$faktura) {
            $faktura = new Faktura();
        }

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
