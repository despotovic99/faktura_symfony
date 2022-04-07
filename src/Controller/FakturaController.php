<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Exceptions\PrinterException;
use App\Form\FakturaType;
use App\Form\StavkaFaktureType;
use App\Services\FakturaDatabaseService;
use App\Services\OrganizacijaDatabaseService;
use App\Services\ProizvodDatabaseService;
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
    private ProizvodDatabaseService $proizvodDBServis;

    public function __construct(FakturaDatabaseService      $fakturaDBServis,
                                FakturaStampanjeServis      $fakturaStampanjeServis,
                                OrganizacijaDatabaseService $organizacijaDBServis,
                                ProizvodDatabaseService     $proizvodDBServis) {
        $this->fakturaDBServis = $fakturaDBServis;
        $this->fakturaStampanjeServis = $fakturaStampanjeServis;
        $this->organizacijaDBServis = $organizacijaDBServis;
        $this->proizvodDBServis = $proizvodDBServis;
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
        $proizvodi=$this->proizvodDBServis->findAll();

//        $form = $this->napraviFormu($organizacije, null);

        return $this->render('faktura/faktura.html.twig', [
//            'form' => $form->createView(),
            'organizacije' => $organizacije,
            'proizvodi'=>$proizvodi
        ]);

    }

    #[Route('/{fakturaId}', name: 'prikazi_fakturu', methods: ['GET'], requirements: ['fakturaId' => '\d+'])]
    public function prikazFakture(int $fakturaId) {

        // mozda korisnik ne sme da vidi fakturu
        $faktura = $this->fakturaDBServis->find($fakturaId);

        $organizacije = $this->organizacijaDBServis->findAll();
        $proizvodi=$this->proizvodDBServis->findAll();

        return $this->render('faktura/faktura.html.twig', [
            'faktura' => $faktura,
            'organizacije' => $organizacije,
            'proizvodi'=>$proizvodi
        ]);
    }

    #[Route('/sacuvaj', name: 'sacuvaj_fakturu', methods: ['POST'])]
    public function sacuvajFakturu(Request $request) {

        $faktura = [
            'id'=>$request->get('id_fakture'),
            'broj_racuna'=>$request->get('broj_racuna'),
            'datum_izdavanja'=>$request->get('datum_izdavanja'),
            'organizacija'=>$request->get('organizacija'),
            'stavke'=>$request->get('stavke'),
        ];

        $poruka = $this->fakturaDBServis->save($faktura);

        $this->addFlash('poruka', $poruka);

        return $this->redirectToRoute('sve_fakture');
    }


    #[Route('/{fakturaId}', name: 'obrisi_fakturu', methods: ['DELETE'] ,requirements: ['fakturaId' => '\d+'])]
    public function obrisiFakturu(int $fakturaId) {

        $faktura = $this->fakturaDBServis->find($fakturaId);

        $poruka = 'Uspesno obrisana faktura!';

        $result = $this->fakturaDBServis->delete($faktura);

        if (!$result) {
            $poruka = 'Problem prilikom brisanja fakture!';
        }

        $this->addFlash('poruka', $poruka);

        return $this->redirectToRoute('sve_fakture');
    }


    #[Route('/{fakturaId}/{formatStampe}', name: 'stampanje_fakture', methods: ['GET'], requirements: ['fakturaId' => '\d+'])]
    public function stampanjeFakture(int $fakturaId, string $formatStampe) {

        try {
            $file = $this->fakturaStampanjeServis->stampaj($fakturaId, $formatStampe);
        } catch (PrinterException $exception) {
            $this->addFlash('poruka', $exception->getMessage());

            return $this->redirectToRoute('prikazi_fakturu', ['fakturaId' => $fakturaId]);
        }

        $putanja = $this->getParameter('download_directory');

        return $this->file($putanja . '/' . $file);
    }


}
