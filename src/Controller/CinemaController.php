<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Form\CinemaType;
use App\Repository\CinemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cinema')]
class CinemaController extends AbstractController
{
    #[Route('/', name: 'cinema_index', methods: ['GET'])]
    public function index(CinemaRepository $cinemaRepository): Response
    {
        return $this->render('cinema/index.html.twig', [
            'cinemas' => $cinemaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'cinema_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $cinema = new Cinema();
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cinema);
            $entityManager->flush();

            return $this->redirectToRoute('cinema_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cinema/new.html.twig', [
            'cinema' => $cinema,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cinema_show', methods: ['GET'])]
    public function show(Cinema $cinema): Response
    {
        return $this->render('cinema/show.html.twig', [
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/edit', name: 'cinema_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Cinema $cinema): Response
    {
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cinema_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cinema/edit.html.twig', [
            'cinema' => $cinema,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cinema_delete', methods: ['POST'])]
    public function delete(Request $request, Cinema $cinema): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cinema->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cinema);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cinema_index', [], Response::HTTP_SEE_OTHER);
    }
}
