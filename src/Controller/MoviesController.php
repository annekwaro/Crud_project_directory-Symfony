<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    #[Route('/movies', methods:['GET'], name: 'movies')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();

        return $this->render('movies/index.html.twig', [
            'controller_name' => 'MoviesController',
            'movies' => $movies,
        ]);
    }
#[Route('/movies/delete/{id}', methods:['GET', 'DELETE'], name: 'delete_movie')]
public function delete($id): Response{
    $movie = $this->movieRepository->find($id);
    $this->em->remove($movie);
    $this->em->flush();
    return $this->redirectToRoute('movies');
}


    #[Route('/movies/{id}', methods: ['GET'], name: 'movie_details')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);

        return $this->render('movies/show.html.twig', [
            'controller_name' => 'MoviesController',
            'movie' => $movie,
        ]);
    }

    #[Route('/movies/edit/{id}', name: 'edit_movie')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagePath = $form->get('imagepath')->getData();

            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    $imagePathToDelete = $this->getParameter('kernel.project_dir') . $movie->getImagePath();

                    if (file_exists($imagePathToDelete)) {
                        unlink($imagePathToDelete);
                    }
                }

                $newFilename = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $movie->setImagePath('/uploads/' . $newFilename);
            }

            $this->em->flush();

            return $this->redirectToRoute('movies');
        }

        return $this->render('movies/edit.html.twig', [
            'controller_name' => 'MoviesController',
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movies/create', name: 'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagePath = $form->get('imagePath')->getData();

            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $movie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($movie);
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }

        return $this->render('movies/create.html.twig', [
            'controller_name' => 'MoviesController',
            'form' => $form->createView(),
        ]);
    }
}