<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book/add', name: 'app_book_add')]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book->setEnabled('true');

            /** @var Author $author */
            $author = $book->getAuthor();

            if ($author) {
                $currentNbBooks = $author->getNbBooks();
                $author->setNbBooks($currentNbBooks + 1);
                $entityManager->persist($author);
            }

            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('app_book_list');

        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/enabled', name: 'app_book_enabled_list')]
    public function listEnabledBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBy(['enabled' => 'true']);

        return $this->render('book/enabled_list.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/list', name: 'app_book_list')]
    public function listBooks(BookRepository $bookRepository): Response
    {
        $publishedBooks = $bookRepository->findBy(['enabled' => 'true']);
        $unpublishedBooks = $bookRepository->findBy(['enabled' => 'false']);

        $publishedCount = count($publishedBooks);
        $unpublishedCount = count($unpublishedBooks);

        return $this->render('book/list.html.twig', [
            'books' => $publishedBooks,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
    }

    #[Route('/book/update/{id}', name: 'app_book_update')]
    public function updateBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/delete/{id}', name: 'app_book_delete')]
    public function deleteBook(Book $book, EntityManagerInterface $entityManager): Response
    {
        /** @var Author|null $author */
        $author = $book->getAuthor();

        $entityManager->remove($book);

        if ($author) {
            $currentNbBooks = $author->getNbBooks();
            if ($currentNbBooks > 0) {
                $author->setNbBooks($currentNbBooks - 1);
                $entityManager->persist($author);
            }
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_book_list');
    }

    #[Route('/book/{id}', name: 'app_book_show')]
    public function showBook(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
