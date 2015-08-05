<?php

namespace Khatovar\Bundle\ContactBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\ContactBundle\Entity\Contact;
use Khatovar\Bundle\ContactBundle\Form\ContactType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeContact = $this->findActiveOr404();

        return $this->render(
            'KhatovarContactBundle:Contact:show.html.twig',
            array('contact' => $activeContact,)
        );
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
        $contact = $this->findByIdOr404($id);

        return $this->render(
            'KhatovarContactBundle:Contact:show.html.twig',
            array('contact' => $contact)
        );
    }

    /**
     * Lists all Contact entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function listAction(Request $request)
    {
        $form = $this->createActivationForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->changeActiveContact($form);

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de contact activée'
            );

            return $this->redirect($this->generateUrl('khatovar_web_contact_list'));
        }

        $contacts    = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->findAll();
        $deleteForms = $this->createDeleteForms($contacts);

        return $this->render(
            'KhatovarContactBundle:Contact:list.html.twig',
            array(
                'contacts'        => $contacts,
                'activation_form' => $form->createView(),
                'delete_forms'    => $deleteForms,
            )
        );
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function newAction()
    {
        $contact = new Contact();

        $form = $this->createCreateForm($contact);

        return $this->render(
            'KhatovarContactBundle:Contact:new.html.twig',
            array('form' => $form->createView(),)
        );
    }

    /**
     * Creates a new Contact entity.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createCreateForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de contact créée'
            );

            return $this->redirect($this->generateUrl('khatovar_web_contact_show', array('id' => $contact->getId())));
        }

        return $this->render(
            'KhatovarContactBundle:Contact:new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction($id)
    {
        $contact = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);

        return $this->render(
            'KhatovarContactBundle:Contact:edit.html.twig',
            array('edit_form' => $editForm->createView(),)
        );
    }

    /**
     * Edits an existing Contact entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function updateAction(Request $request, $id)
    {
        $contact = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de contact mise à jour'
            );

            return $this->redirect($this->generateUrl('khatovar_web_contact_edit', array('id' => $id)));
        }

        return $this->render(
            'KhatovarContactBundle:Contact:edit.html.twig',
            array('edit_form' => $editForm->createView(),)
        );
    }

    /**
     * Deletes a Contact entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact = $this->findByIdOr404($id);
            $this->entityManager->remove($contact);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de contact supprimée'
            );
        }

        return $this->redirect($this->generateUrl('khatovar_web_contact_list'));
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
                'action' => $this->generateUrl('khatovar_web_contact_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Créer'));

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
                'action' => $this->generateUrl('khatovar_web_contact_update', array('id' => $contact->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

        return $form;
    }

    /**
     * Creates a form to delete a Contact entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_contact_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Effacer',
                    'attr'  => array('onclick' => 'return confirm("Êtes-vous sûr ?")'),
                )
            )
            ->getForm();
    }

    /**
     * Return a list of delete forms for a set of contacts.
     *
     * @param Contact[] $contacts
     *
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $contacts)
    {
        $deleteForms = array();

        foreach ($contacts as $contact) {
            $deleteForms[$contact->getId()] = $this->createDeleteForm($contact->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * @param int $id
     *
     * @return Contact
     */
    protected function findByIdOr404($id)
    {
        $contact = $this->entityManager->getRepository('KhatovarContactBundle:Contact')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Impossible de trouver le contact.');
        }

        return $contact;
    }

    /**
     * @return Contact
     */
    protected function findActiveOr404()
    {
        $contact = $this->entityManager
            ->getRepository('KhatovarContactBundle:Contact')
            ->findOneBy(array('active' => true));

        if (null === $contact) {
            throw new NotFoundHttpException('There is no active Contact entity. You must activate one.');
        }

        return $contact;
    }

    /**
     * Create a form to activate a Homepage.
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createActivationForm()
    {
        $form = $this->createFormBuilder()
            ->add(
                'active',
                'entity',
                array(
                    'class'    => 'Khatovar\Bundle\ContactBundle\Entity\Contact',
                    'label'    => false,
                    'property' => 'title',
                )
            )
            ->add('submit', 'submit', array('label' => 'Activer'))
            ->getForm();

        return $form;
    }

    /**
     * Change the active Homepage.
     *
     * @param FormInterface $form
     */
    protected function changeActiveContact(FormInterface $form)
    {
        $repository = $this->entityManager->getRepository('KhatovarContactBundle:Contact');
        $newContact = $repository->find($form->get('active')->getData());
        $oldContact = $repository->findOneBy(array('active' => true));

        if (null !== $oldContact) {
            $oldContact->setActive(false);
            $this->entityManager->persist($oldContact);
        }

        $newContact->setActive(true);
        $this->entityManager->persist($newContact);
        $this->entityManager->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Page de contact activée');
    }
}
