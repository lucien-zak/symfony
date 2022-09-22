<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/profile', name:'app_profile')]
function index(Request $request): Response
    {
    $currentUser = $this->getUser();
    $form = $this->createForm(ProfileFormType::class, $currentUser);
    if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
       dd('ok');

        // ... perform some action, such as saving the task to the database

        return $this->redirectToRoute('task_success');
    }
    

    return $this->render('user/index.html.twig', [
        'form' => $form->createView(),
    ]);}
}
