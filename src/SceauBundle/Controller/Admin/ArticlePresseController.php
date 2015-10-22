<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SceauBundle\Entity\ArticlePresse;
use SceauBundle\Form\Type\Admin\ArticlePresseType;

/**
 * ArticlePresse controller.
 *
 * @Route("/articles")
 */
class ArticlePresseController extends Controller
{

    /**
     * List all published Articles.
     *
     * @Route("/", name="articles")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Articles:index.html.twig")
     */
    public function indexAction()
    {
        $entityRepo = $this->get('sceau.repository.article.presse');
        $entities = $entityRepo->findBy(array(), array('date' => 'ASC'));

        return array(
            'entities' => $entities
        );
    }

    /**
     * Creates a new ArticlePresse entity.
     *
     * @Route("/", name="article_create")
     * @Method("POST")
     * @Template("SceauBundle:Admin/Articles:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $session = $request->getSession();
        $entity = new ArticlePresse();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Article  $title bien ajouté");

            return $this->redirect($this->generateUrl('articles'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ArticlePresse entity.
     *
     * @param ArticlePresse $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ArticlePresse $entity)
    {
        $form = $this->createForm(new ArticlePresseType(), $entity, array(
            'action' => $this->generateUrl('article_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new ArticlePresse entity.
     *
     * @Route("/new", name="article_new")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Articles:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $entity = new ArticlePresse();
        $form   = $this->createCreateForm($entity);


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ArticlePresse entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Articles:show.html.twig")
     */
    public function showAction($id)
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $entity = $articlePresseRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticlePresse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ArticlePresse entity.
     *
     * @Route("/{id}/edit", name="article_edit")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Articles:edit.html.twig")
     */
    public function editAction($id)
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $entity = $articlePresseRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticlePresse entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a ArticlePresse entity.
    *
    * @param ArticlePresse $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ArticlePresse $entity)
    {
        $form = $this->createForm(new ArticlePresseType(), $entity, array(
            'action' => $this->generateUrl('article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing ArticlePresse entity.
     *
     * @Route("/{id}", name="article_update")
     * @Method("PUT")
     * @Template("SceauBundle:ArticlePresse:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $entity = $articlePresseRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticlePresse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Article $title bien modifié");

            return $this->redirect($this->generateUrl('articles'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ArticlePresse entity.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     * @Template("SceauBundle:Admin/Articles:delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $session = $request->getSession();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $articlePresseRepo = $this->get('sceau.repository.article.presse');
            $entity = $articlePresseRepo->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ArticlePresse entity.');
            }

            $em->remove($entity);
            $em->flush();
            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Article $title bien supprimé");

            return $this->redirect($this->generateUrl('articles'));
        }

        return array(
            'delete_form' => $form->createView(),
        );

    }

    /**
     * Creates a form to delete a ArticlePresse entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer', 'attr' => array('class' => 'btn btn-red')))
            ->getForm()
        ;
    }
}
