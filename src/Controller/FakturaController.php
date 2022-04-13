<?php

namespace App\Controller;

use App\Exceptions\PrinterException;
use App\Services\FakturaDatabaseService;
use App\Services\OrganizacijaDatabaseService;
use App\Services\ProizvodDatabaseService;
use App\Services\Stampanje\FakturaStampanjeServis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/f', name: 'asd', methods: ['GET'])]
    public function index2(): Response {


        return $this->render('faktura/prikaz_fakture.html.twig');
    }


    #[Route('/unos', name: 'unos_fakture', methods: ['GET'])]
    public function novaFaktura(Request $request): Response {

        $organizacije = $this->organizacijaDBServis->findAll();
        $proizvodi = $this->proizvodDBServis->findAll();

        $fakturaId=$request->get('id_fakture');

        $faktura=null;
        if($fakturaId){
        $faktura = $this->fakturaDBServis->find($fakturaId);
        }

        return $this->render('faktura/unos_fakture.html.twig', [
            'faktura' => $faktura,
            'organizacije' => $organizacije,
            'proizvodi' => $proizvodi
        ]);

    }

    #[Route('/{fakturaId}', name: 'prikazi_fakturu', methods: ['GET'], requirements: ['fakturaId' => '\d+'])]
    public function prikazFakture(int $fakturaId) {

        $faktura = $this->fakturaDBServis->find($fakturaId);

        return $this->render('faktura/prikaz_fakture.html.twig', [
            'faktura' => $faktura
        ]);
    }

    #[Route('/sacuvaj', name: 'sacuvaj_fakturu', methods: ['POST'])]
    public function sacuvajFakturu(Request $request) {

        $faktura = [
            'id' => $request->get('id_fakture'),
            'broj_racuna' => $request->get('broj_racuna'),
            'datum_izdavanja' => $request->get('datum_izdavanja'),
            'organizacija' => $request->get('organizacija'),
            'stavke' => $request->get('stavke'),
        ];

        [$poruka, $fakturaId] = $this->fakturaDBServis->save($faktura);

        $this->addFlash('poruka', $poruka);

        if ($fakturaId) {

            return $this->redirectToRoute('prikazi_fakturu', ['fakturaId' => $fakturaId]);
        }

        return $this->redirectToRoute('nova_faktura');
    }


    #[Route('/{fakturaId}', name: 'obrisi_fakturu', methods: ['DELETE'], requirements: ['fakturaId' => '\d+'])]
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

// todo kako stampati novu fakturu, jos uvek nesacuvanu?
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
