<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Paginator;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Affiche la liste de tous les commentaires
     * 
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     * 
     * @param CommentRepository $repo
     * @return Response
     */
    public function index(CommentRepository $repo, $page, Paginator $paginator)
    {
        $paginator->setEntityClass(Comment::class)
                  ->setCurrentPage($page)
        ;
        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $paginator
        ]);
    }

    /**
     * Permet la modification d'un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * 
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "Le commentaire a bien été modifié"
            );
        }
        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Supprime un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")     * 
     * 
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return void
     */
    public function delete(Comment $comment, ObjectManager $manager) {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash(
            'success',
            "Le commentaire a bien été supprimé"
        );

        return $this->redirectToRoute("admin_comments_index");
    }
}
