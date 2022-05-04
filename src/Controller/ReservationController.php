<?php

namespace App\Controller;
use Twilio\Rest\Client;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\HebergementRepository;
use App\Repository\ReservationRepository;
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

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController

{
    const  ATTRIBUTES_TO_SERIALIZE = ['id', 'date', 'nom', 'prenom', 'list_hotel', 'prix'];
    /**
     * @Route("/", name="app_reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findBy(array(),array('prix' =>'Asc')),
        ]);
    }

    /**
     * @Route("/new", name="app_reservation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //* $user=$this->getUser();
            //*je crre une variable : $nom = $user->getNom();
            //*je crre une variable : $prenom = $user->getPrenom();
            //* $reservation->setNom($nom);
          //*  $reservation->setNom($nom);

            $reservationRepository->add($reservation);
            $sid    = "AC26b74eb349f20ee3528c91fd33583179";
            $token  = "97a7ccf5fcf81f019710de9b4424f80b";
            $twilio = new Client($sid, $token);

            $message = $twilio->messages
                ->create("+21629553124", // to
                    array(
                        "messagingServiceSid" => "MG4d1b036387827258da9c8f1818be6b05",
                        "body" => "votre reservation a ete effectue avec succes " //* + $form->get('listhotel')->getData();

                    )
                );

            print($message->sid);
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_reservation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->add($reservation);
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reservation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/tri/prix", name="tri")
     */

    public function Tri(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Reservation a
            ORDER BY a.prix DESC'
        );


        $rep = $query->getResult();

        return $this->render('reservation/index.html.twig',
            array('reservations' => $rep));

    }
    /**
     * @Route("/ajouter/reservation" , name="reservation_ajouter" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function ajouter(Request $request, SerializerInterface $serializer,HebergementRepository $repo)
    {

        $reservation = new reservation ();
        $date = $request->query->get('date');
        $ymd = new \DateTime($date);
        $nom = $request->query->get('nom');
        $prenom = $request->query->get('prenom');
        $listHotel = $request->query->get('listHotel');
        $prix = $request->query->get('prix');

        $em = $this->getDoctrine()->getManager();

        $reservation->setDate($ymd);
        $hebergementId=$request->query->get('hebergementId');
        $hebergement=$repo->findOneById($hebergementId);

        $reservation->setHebergement($hebergement);
        $reservation->setNom($nom);
        $reservation->setPrenom($prenom);
        $reservation->setListHotel($listHotel);
        $reservation->setPrix($prix);

        $em->persist($reservation);
        $em->flush();
        $json = $serializer->serialize(
            $reservation,
            'json',
             ['groups' => ['reservation' ]]
        );
      //  $serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted = $serializer->normalize($reservation);
        return new JsonResponse($json);
    }


    /**
     * @Route("/modifier/reservation/{id}" , name="reservation_modifier" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */

    public function modifer(Request $request,$id,SerializerInterface $serializer,ReservationRepository $repo)
    {

        $reservation=$repo->findOneById($id);
        $date=$request->query->get('date');
        $ymd = new \DateTime($date);
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $listHotel=$request->query->get('listHotel');
        $prix=$request->query->get('prix');

        $em=$this->getDoctrine()->getManager();
        $reservation->setDate($ymd);
        $reservation->setNom($nom);
        $reservation->setPrenom($prenom);
        $reservation->setlistHotel($listHotel);

        $reservation->setPrix($prix);

        $em->persist($reservation);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($reservation);
        return new JsonResponse($formatted);
    }




    /**
     * @Route("/afficher/reservation" , name="reservation_afficher" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */

    public function afficher(Request $request, SerializerInterface $serializer, ReservationRepository $repo)
    {

        $reservation = $repo->findOneById( $request->query->get('id'));
        $json = $serializer->serialize($reservation, 'json', ['groups' => ['reservation']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['reservation' => $reservation], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }

    /**
     * @Route("/delete/json", name="supprimer_Reservation")
     */
    public function supprimerReservation(Request $request, ReservationRepository $repo): Response
    {

        $id =$request->get("id");
        $em=$this->getDoctrine()->getManager();

        $id=   $repo->find($id);

        if($id != null){
            $em->remove($id);
            $em->flush();
            $serializer=new Serializer([new ObjectNormalizer()]);
            $formatted=$serializer->normalize("votre reservation a été annulé avec succes  ");
            return new JsonResponse($formatted);
        }

        return  new JsonResponse("Id Invalide");
    }







    /**
     * @Route("/reservation/list")
     * @param ReservationRepository $repo
     */
    public function getList(ReservationRepository $repo, SerializerInterface $serializer): Response
    {

        $reservations = $repo->findAll();
        $json = $serializer->serialize($reservations, 'json', ['groups' => ['reservation']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['reservation' => $reservations], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }






}
