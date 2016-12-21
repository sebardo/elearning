<?php

namespace ElearningBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ElearningBundle\Entity\Course;
use ElearningBundle\Form\CourseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Course controller.
 *
 * @Route("/admin/course")
 */
class CourseController extends Controller
{

    /**
     * Lists all Course entities.
     *
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * Returns a list of Course entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/list.{_format}", requirements={ "_format" = "json" }, defaults={ "_format" = "json" })
     * @Method("GET")
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adminManager = $this->get('admin_manager');
        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('ElearningBundle:Course'));
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $siteIds = $adminManager->getSiteIds(); 
            $jsonList->setSiteIds($siteIds);
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Creates a new Course entity.
     *
     * @Route("/")
     * @Method("POST")
     * @Template("ElearningBundle:Course:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Course();
        $form = $this->createNewForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'course.created');

            return $this->redirect($this->generateUrl('elearning_course_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Course entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Course();
        $form   = $this->createNewForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Course entity.
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
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
    * Creates a form to create a Course entity.
    *
    * @param Course $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    public function createNewForm(Course $entity)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $formConfig = array(
            'action' => $this->generateUrl('elearning_course_create'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal form-row-seperated'),
        );
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['siteIds'] = $this->get('admin_manager')->getSiteIds(); 
        }
        $form = $this->createForm('ElearningBundle\Form\CourseType', $entity, $formConfig);

        return $form;
    }

    /**
    * Creates a form to edit a Course entity.
    *
    * @param Course $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Course $entity)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $formConfig = array(
            'action' => $this->generateUrl('elearning_course_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ); 
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['siteIds'] = $this->get('admin_manager')->getSiteIds(); 
        }
        $form = $this->createForm('ElearningBundle\Form\CourseType', $entity, $formConfig);

        return $form;
    }
    /**
     * Edits an existing Course entity.
     *
     * @Route("/{id}")
     * @Method("PUT")
     * @Template("ElearningBundle:Course:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'course.updated');

            return $this->redirect($this->generateUrl('elearning_course_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Course entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Actor $entity */
            $entity = $em->getRepository('ElearningBundle:Course')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Course entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('danger', 'course.deleted');
        }

        return $this->redirect($this->generateUrl('elearning_course_index'));
    }

    /**
     * Creates a form to delete a Course entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->setAction($this->generateUrl('elearning_course_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('id', HiddenType::class)
            ->getForm()
        ;
    }

}
