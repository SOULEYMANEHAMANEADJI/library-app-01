<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function create(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        return $this->render('book/add.html.twig', [
            'authors' => $authors,
        ]);
    }
    #[Route('/book/add', name: 'add_book')]
    public function add(Request $request, EntityManagerInterface $em, AuthorRepository $authorRepository): Response
    {
        $title = $request->request->get('title');
        $isbn = $request->request->get('isbn');
        $author_id = $request->request->get('author');
        $author  = $authorRepository->find($author_id);
        // dd($author);
        $book = new Book();
        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setAuthor($author);
        $em->persist($book);
        $em->flush();
        return $this->redirectToRoute('list_book');
    }
    #[Route('/book/list', name: 'list_book')]
    public function list(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        // dd($authors);
        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/book/update', name: 'update_book')]
    public function update(): Response
    {
        return $this->render('book/list.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/book/edit/{id}', name: 'edit_book')]
    public function edit($id, BookRepository $bookRepository, AuthorRepository $authorRepository, Request $request, EntityManagerInterface $em): Response
    {
        $book = $bookRepository->find($id);
        // Si le livre n'est pas trouvé, renvoyer une erreur 404
        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }
        // Récupérer tous les auteurs pour afficher dans le formulaire
        $authors  = $authorRepository->findAll();
        // dd($book);
        // dd($authors);
        // Si le formulaire est soumis (méthode POST)
        if ($request->isMethod('POST')) {

            $authorId = $request->request->get('author');

            $author = $authorRepository->find($authorId);

            $book->setTitle($request->request->get('title'));
            $book->setIsbn($request->request->get('isbn'));
            $book->setAuthor($author);

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('list_book');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'authors' => $authors,
        ]);
    }
    #[Route('/book/delete/{id}', name: 'delete_book')]
    public function delete($id, EntityManagerInterface $em, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('list_book');
    }
}
