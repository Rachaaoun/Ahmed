<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Hebergement;
use App\Form\HebergementType;
use App\Form\SearchFormType;
use App\Repository\HebergementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;



/**
 * @Route("/hebergement")
 */
class HebergementController extends AbstractController

{
    const  ATTRIBUTES_TO_SERIALIZE = ['id', 'nom', 'adresse', 'type', 'nbrChambre', 'typeChambre'];

    /**
     * @Route("/", name="app_hebergement_index", methods={"GET"})
     */
    public function index(HebergementRepository $hebergementRepository, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);

        $form->handleRequest($request);

        $Hebergement = $hebergementRepository->findSearch($data);
        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $Hebergement, 'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="app_hebergement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, HebergementRepository $hebergementRepository): Response
    {
        $hebergement = new Hebergement();
        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hebergementRepository->add($hebergement);
            return $this->redirectToRoute('app_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hebergement/new.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_hebergement_show", methods={"GET"})
     */
    public function show(Hebergement $hebergement): Response
    {
        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $hebergement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_hebergement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Hebergement $hebergement, HebergementRepository $hebergementRepository): Response
    {
        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hebergementRepository->add($hebergement);
            return $this->redirectToRoute('app_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hebergement/edit.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_hebergement_delete", methods={"POST"})
     */
    public function delete(Request $request, Hebergement $hebergement, HebergementRepository $hebergementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $hebergement->getId(), $request->request->get('_token'))) {
            $hebergementRepository->remove($hebergement);
        }

        return $this->redirectToRoute('app_hebergement_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/hebergement/list")
     * @param HebergementRepository $repo
     */
    public function getList(HebergementRepository $repo, SerializerInterface $serializer): Response
    {

        $hebergements = $repo->findAll();
        $json = $serializer->serialize($hebergements, 'json', ['groups' => ['hebergement']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['hebergement' => $hebergements], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }


    /**
     * @Route("/ajouter/hebergement" , name="hebergement_ajouter" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function ajouter(Request $request, SerializerInterface $serializer)
    {

        $hebergement = new Hebergement();
        $nom = $request->query->get('nom');
        $adresee = $request->query->get('adresse');
        $type = $request->query->get('type');
        $nbrChambre = $request->query->get('nbrChambre');
        $typeChambre = $request->query->get('typeChambre');

        $em = $this->getDoctrine()->getManager();

        $hebergement->setNom($nom);
        $hebergement->setAdresse($adresee);
        $hebergement->setType($type);
        $hebergement->setNbrChambre($nbrChambre);
        $hebergement->setTypeChambre($typeChambre);

        $em->persist($hebergement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($hebergement);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/modifier/hebergement/{id}" , name="hebergement_modifier" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function modifier(Request $request,$id, SerializerInterface $serializer, HebergementRepository $repo)
    {

        $hebergement = new Hebergement();
        $hebergement = $repo->findOneById($id);
        $nom = $request->query->get('nom');
        $adresse = $request->query->get('adresse');
        $type = $request->query->get('type');
        $nbrChambre = $request->query->get('nbrChambre');
        $typeChambre = $request->query->get('typeChambre');

        $em = $this->getDoctrine()->getManager();

        $hebergement->setNom($nom);
        $hebergement->setAdresse($adresse);
        $hebergement->setType($type);
        $hebergement->setNbrChambre($nbrChambre);
        $hebergement->setTypeChambre($typeChambre);


        $em->persist($hebergement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($hebergement);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/afficher/hebergement" , name="hebergement_afficher" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */

    public function afficher(Request $request, SerializerInterface $serializer, HebergementRepository $repo)
    {

        $hebergements = $repo->findOneById($request->query->get('id'));
        $json = $serializer->serialize($hebergements, 'json', ['groups' => ['hebergement']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['hebergement' => $hebergements], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }

    /**
     * @Route("/delete/json", name="supprimer_Hebergement")
     */
    public function supprimerHebergement(Request $request, HebergementRepository $repo): Response
    {

        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();

        $id = $repo->find($id);

        if ($id != null) {
            $em->remove($id);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize("les informations ont ete supprimer ");
            return new JsonResponse($formatted);
        }

        return new JsonResponse("Id Invalide");
    }


    /*/**
     * @Route("/pdf", name="pdf", methods={"GET"})
     */
  /*  public function list(HebergementRepository $hebergementRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');


        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfoptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('hebergement/listt.html.twig', [
            'b' => $hebergementRepository->findAll(),
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);

    }*/

    /*/**
     * @Route("/pdf/download", name="app_pdf_download")
  /*   */
   /* public function download(HebergementRepository $repository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $hebergements = $repository->findAll();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('hebergement/PDF.html.twig',[' hebergements'=> $hebergements]);


        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);

        exit(0);
    }*/



















}



















