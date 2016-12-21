<?php

namespace ElearningBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ElearningBundle\Entity\Ask;
use ElearningBundle\Form\AskType;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Ask controller.
 *
 * @Route("/admin/test/{test}/ask")
 */
class AskController extends Controller
{

    /**
     * Lists all Ask entities.
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
     * Returns a list of Ask entities in JSON format.
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
        $jsonList->setRepository($em->getRepository('ElearningBundle:Ask'));
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $siteIds = $adminManager->getSiteIds(); 
            $jsonList->setSiteIds($siteIds);
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Creates a new Ask entity.
     *
     * @Route("/")
     * @Method("POST")
     * @Template("ElearningBundle:Ask:new.html.twig")
     */
    public function createAction(Request $request, $test)
    {
        $entity = new Ask();
        $form = $this->createNewForm($entity, $test);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $testEntity = $em->getRepository('ElearningBundle:Test')->find($test);

            if (!$testEntity) {
                throw $this->createNotFoundException('Unable to find Test entity.');
            }
            $entity->setTest($testEntity);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'ask.created');

            return $this->redirect($this->generateUrl('elearning_test_edit', array('id' => $test, 'ask' => true)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Ask entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($test)
    {
        $entity = new Ask();
        $form   = $this->createNewForm($entity, $test);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Sorts a list of features.
     *
     * @param Request $request
     * @param int     $categoryId
     *
     * @throws NotFoundHttpException
     * @return array|Response
     *
     * @Route("/sort")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            $this->get('admin_manager')->sort('CoreBundle:Ask', $request->get('values'));

            return new Response(0, 200);
        }

        $asks = $em->getRepository('CoreBundle:Ask')->findBy(
            array('order' => 'asc')
        );

        return array(
            'asks' => $asks
        );
    }
    
    /**
     * Finds and displays a Ask entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Ask')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ask entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Ask entity.
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Ask')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ask entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );
    }

    
    /**
    * Creates a form to create a Ask entity.
    *
    * @param Ask $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    public function createNewForm(Ask $entity, $test)
    {
        $formConfig = array(
            'action' => $this->generateUrl('elearning_ask_create', array('test' => $test) ),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ); 
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['siteIds'] = $this->get('admin_manager')->getSiteIds(); 
        }
        $form = $this->createForm('\ElearningBundle\Form\AskType', $entity, $formConfig);
        return $form;
    }

    /**
    * Creates a form to edit a Ask entity.
    *
    * @param Ask $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ask $entity, $test)
    {
        $formConfig = array(
            'action' => $this->generateUrl('elearning_test_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ); 
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['site'] = $user->getSite()->getId();
        }
        $form = $this->createForm('\ElearningBundle\Form\AskType', $entity, $formConfig);
        return $form;
    }
    /**
     * Edits an existing Ask entity.
     *
     * @Route("/{id}")
     * @Method("PUT")
     * @Template("ElearningBundle:Ask:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Ask')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ask entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'ask.updated');

            return $this->redirect($this->generateUrl('elearning_test_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Ask entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Actor $entity */
            $entity = $em->getRepository('ElearningBundle:Ask')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ask entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('danger', 'ask.deleted');
        }

        return $this->redirect($this->generateUrl('elearning_test_index'));
    }

    /**
     * Creates a form to delete a Ask entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->setAction($this->generateUrl('elearning_test_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('id', HiddenType::class)
            ->getForm()
        ;
    }

}
