<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/film')]
class FilmController extends AbstractController
{
    #[Route('/', name: 'film_index', methods: ['GET'])]
    public function index(FilmRepository $filmRepository): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'film_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('film/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }
       
    #[Route ("/task", name: 'film_task', methods:['GET','POST'])]
     public function task(FilmRepository $filmRepository): Response
    {
        $id = random_int(1,6);
        $films = $filmRepository->findBy(['id'=>"$id"]);
        return $this->render('film/task.html.twig', [
            'films' => $films
        ]);
    }

    #[Route('/{id}', name: 'film_show', methods: ['GET'])]
    public function show(Film $film): Response
    {
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/{id}/edit', name: 'film_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Film $film): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('film/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'film_delete', methods: ['POST'])]
    public function delete(Request $request, Film $film): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('film_index', [], Response::HTTP_SEE_OTHER);
    }
}
