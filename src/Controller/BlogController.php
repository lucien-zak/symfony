<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Form\ArticleType;
use App\Form\CommentType;
use DateTime;
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
    return $this->redirectToRoute('articles');
    // return $this->render('blog/home.html.twig');
}

#[Route('/articles', name:'articles')]
function articles(ManagerRegistry $doctrine, Request $request): Response
    {
    dump($this->getUser());
    $pages = $request->get('page', 1);
    $entityManager = $doctrine->getManager();
    $articles = $entityManager->getRepository(Articles::class)
        ->findBy(array(), array(
            'createdAt' => 'DESC',
        ), 5, ($pages - 1) * 5);
    $articlesNumber = count($entityManager->getRepository(Articles::class)->findAll());
    $numberPages = ceil($articlesNumber / 5);
    // dd($articles[0]->getUsers()->getLogin());

    return $this->render('blog/articles.html.twig', [
        'articles' => $articles,
        'nbPages' => (int) $numberPages,
    ]);
}

#[Route('/article/{id}', name:'article')]
function article(ManagerRegistry $doctrine, $id, Request $request): Response
    {
    $entityManager = $doctrine->getManager();
    $article = $entityManager->getRepository(Articles::class)->find($id);
    $comment = new Comments();
    $comment->setUser($this->getUser());
    $comment->setArticles($article);
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    return $this->render('blog/article.html.twig', [
        'article' => $article,
        'commentTypeForm' => $form->createView(),
    ]);

}
#[Route('/newarticle', name:'article_form')]
function newArticle(ManagerRegistry $doctrine, Request $request): Response
    {
        $article = new Articles();
        $article->setCreatedAt(\DateTimeImmutable::createFromMutable(new DateTime()))
        ->setUser($this->getUser());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Image')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = "/".$this->getParameter('image_path').$safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('image_path'),
                    $newFilename
                );
                $article->setImage($newFilename);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('articles');
        }
        
    return $this->render('blog/articleForm.html.twig', [
        'test' => 'test',
        'artcleFormType' => $form->createView() 
    ]);
}
}
