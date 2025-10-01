<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/show.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/{name}', name: 'author_show')]
    public function showAuthor(string $name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,
        ]);
    }


    #[Route('/authors', name: 'author_list')]
    public function listAuthors(): Response
    {
        $listAuthors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        $authors = array_map(function (array $a) {
            $username = $a['username'];
            $a['username_upper'] = mb_strtoupper($username, 'UTF-8');
            $a['detailsUrl'] = $this->generateUrl('author_details', ['id' => $a['id']]);
            return $a;
        }, $listAuthors);

        $authorCount = count($authors);
        $totalBooks = array_sum(array_column($authors, 'nb_books'));

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
            'authorCount' => $authorCount,
            'totalBooks' => $totalBooks,
        ]);
    }

    #[Route('/author/details/{id}', name: 'author_details', requirements: ['id' => '\d+'])]
    public function authorDetails(int $id): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        foreach ($authors as $author) {
            if ((int) $author['id'] === $id) {
                return $this->render('author/showAuthor.html.twig', [
                    'author' => $author,
                ]);
            }
        }
    }
}
