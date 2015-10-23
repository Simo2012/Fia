<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SceauBundle\Entity\Actualite;
use SceauBundle\Form\Type\Admin\ActualiteType;

/**
 * Actualite controller.
 *
 * @Route("/actualites")
 */
class ActualiteController extends Controller
{

    /**
     * Lists all Actualite entities.
     *
     * @Route("/", name="actualites")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Actualite:index.html.twig")
     */
    public function indexAction()
    {
        $entityRepo = $this->get('sceau.repository.actualite');
        $entities = $entityRepo->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Actualite entity.
     *
     * @Route("/", name="actualites_create")
     * @Method("POST")
     * @Template("SceauBundle:Admin/Actualite:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $session = $request->getSession();
        $entity = new Actualite();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Actualité  $title bien ajoutée");

            return $this->redirect($this->generateUrl('actualites'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Actualite entity.
     *
     * @param Actualite $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Actualite $entity)
    {
        $form = $this->createForm(new ActualiteType(), $entity, array(
            'action' => $this->generateUrl('actualites_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Actualite entity.
     *
     * @Route("/new", name="actualites_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Actualite();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Actualite entity.
     *
     * @Route("/{id}", name="actualites_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $entityRepo = $this->get('sceau.repository.actualite');

        $entity = $entityRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actualite entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Actualite entity.
     *
     * @Route("/{id}/edit", name="actualites_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $entityRepo = $this->get('sceau.repository.actualite');

        $entity = $entityRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actualite entity.');
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
    * Creates a form to edit a Actualite entity.
    *
    * @param Actualite $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Actualite $entity)
    {
        $form = $this->createForm(new ActualiteType(), $entity, array(
            'action' => $this->generateUrl('actualites_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Actualite entity.
     *
     * @Route("/{id}", name="actualites_update")
     * @Method("PUT")
     * @Template("SceauBundle:Admin/Actualite:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $entityRepo = $this->get('sceau.repository.actualite');

        $entity = $entityRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actualite entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Actualité $title bien modifiée");

            return $this->redirect($this->generateUrl('actualites'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Actualite entity.
     *
     * @Route("/{id}", name="actualites_delete")
     * @Method("DELETE")
     * @Template("SceauBundle:Admin/Actualite:delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $session = $request->getSession();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entityRepo = $this->get('sceau.repository.actualite');
            $entity = $entityRepo->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Actualité entity.');
            }

            $em->remove($entity);
            $em->flush();
            $title = $entity->getTitle();
            $session->getFlashBag()->add('info', "Actualité $title bien supprimée");

            return $this->redirect($this->generateUrl('actualites'));
        }

        return array(
            'delete_form' => $form->createView(),
        );
    }

    /**
     * Creates a form to delete a Actualite entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actualites_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer', 'attr' => array('class' => 'btn btn-red')))
            ->getForm()
        ;
    }
}
