<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function create(): Response
    {
        // return new Response('Salaam' . $request->query->get('name', 'monitor'));
        return $this->render('author/add.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/author/add', name: 'add_author')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('name');
        $author = new Author();
        $author->setName($name);
        $em->persist($author);
        $em->flush();
         return $this->redirectToRoute('list_author');
    }
    #[Route('/author/list', name: 'list_author')]
    public function list(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        // dd($authors);
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }
    #[Route('/author/update', name: 'update_author')]
    public function update(): Response
    {
        return $this->render('author/list.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/author/edit/{id}', name: 'edit_author')]
    public function edit($id, AuthorRepository $authorRepository, Request $request, EntityManagerInterface $em): Response
    {
        $author = $authorRepository->find($id);
        if (!$author) {
            throw $this->createNotFoundException('No author found for id '.$id);
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $author->setName($name);
            $em->flush();

            return $this->redirectToRoute('list_author');
        }
        return $this->render('author/edit.html.twig', [
            'author' => $author,
        ]);
    }
    #[Route('/author/delete/{id}', name: 'delete_author')]
    public function delete($id, AuthorRepository $authorRepository, EntityManagerInterface $em): Response
    {
        $author = $authorRepository->find($id);
        if (!$author) {
            throw $this->createNotFoundException('No author found for id '.$id);
        }
        $em->remove($author);
        $em->flush();
        // Rediriger vers la liste des auteurs après la suppression
        return $this->redirectToRoute('list_author');
        /*return $this->render('author/delete.html.twig', [
            'controller_name' => 'AuthorController',
        ]);*/
    }
}