<?php

namespace Khatovar\Bundle\ContactBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\ContactBundle\Entity\Contact;
use Khatovar\Bundle\ContactBundle\Form\ContactType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Main controller for Contact bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContactController extends Controller
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container     = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * Display the default Contact entity.
     */
    public function indexAction()
    {
        $activeContact = $this->entityManager
            ->getRepository('KhatovarContactBundle:Contact')
            ->findOneBy(array('active' => true));

        if (null === $activeContact) {
            throw new NotFoundHttpException('There is no active Contact entity. You must activate one.');
        }

        return $this->showAction($activeContact->getId());
    }

    /**
     * Lists all Contact entities.
     */
    public function listAction()
    {
        $contacts = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->findAll();

        return $this->render(
            'KhatovarContactBundle:Contact:index.html.twig',
            array(
                'contacts' => $contacts,
            )
        );
    }

    /**
     * Creates a new Contact entity.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createCreateForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('contact_show', array('id' => $contact->getId())));
        }

        return $this->render(
            'KhatovarContactBundle:Contact:new.html.twig',
            array('form' => $form->createView(),)
        );
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $contact = new Contact();
        $form = $this->createCreateForm($contact);

        return $this->render('KhatovarContactBundle:Contact:new.html.twig', array(
            'contact' => $contact,
            'form'    => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Contact contact.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $contact = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Unable to find the contact.');
        }

        $deleteForm = $this->createDeleteForm($contact);

        return $this->render(
            'KhatovarContactBundle:Contact:show.html.twig',
            array(
                'contact'     => $contact,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $contact = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Unable to find the contact.');
        }

        $editForm   = $this->createEditForm($contact);
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render(
            'KhatovarContactBundle:Contact:edit.html.twig',
            array(
                'contact'     => $contact,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Edits an existing Contact entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $contact = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Unable to find the contact.');
        }

        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createEditForm($contact);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('contact_edit', array('id' => $id)));
        }

        return $this->render(
            'KhatovarContactBundle:Contact:edit.html.twig',
            array(
                'contact'     => $contact,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Contact entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $contact = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Unable to find the contact.');
        }

        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();
        }

        return $this->redirect($this->generateUrl('contact_list'));
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Contact $contact The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCreateForm(Contact $contact)
    {
        $form = $this->createForm(
            new ContactType(),
            $contact,
            array(
                'action' => $this->generateUrl('contact_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a Contact entity.
     *
     * @param Contact $contact The entity ID
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createEditForm(Contact $contact)
    {
        $form = $this->createForm(
            new ContactType(),
            $contact,
            array(
                'action' => $this->generateUrl('contact_update', array('id' => $contact->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Contact entity.
     *
     * @param Contact $contact
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm(Contact $contact)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
