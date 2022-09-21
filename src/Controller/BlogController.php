<?php

namespace App\Controller;

use App\Entity\Articles;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name:'home')]
function home(): Response
    {
    return $this->render('blog/home.html.twig');
}

#[Route('/articles', name:'articles')]
function articles(ManagerRegistry $doctrine, Request $request): Response
    {
    dump($this->getUser());
    $pages = $request->get('page', 1);
    $entityManager = $doctrine->getManager();
    $articles = $entityManager->getRepository(Articles::class)
        ->findBy(array(), array(
            'createdAt' => 'DESC'
        ), 5, ($pages -1 ) * 5 );
    $articlesNumber = count($entityManager->getRepository(Articles::class)->findAll());
    $numberPages = ceil($articlesNumber / 5);
    // dd($articles[0]->getUsers()->getLogin());

    return $this->render('blog/articles.html.twig', [
        'articles' => $articles,
        'nbPages' => (int)$numberPages
    ]);
}

#[Route('/article/{id}', name:'article')]
function article(ManagerRegistry $doctrine, $id): Response
    {
    $entityManager = $doctrine->getManager();
    $article = $entityManager->getRepository(Articles::class)->find($id);
    return $this->render('blog/article.html.twig', [
        'article' => $article,
    ]);

}
}
