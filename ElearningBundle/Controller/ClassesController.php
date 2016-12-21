<?php

namespace ElearningBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ElearningBundle\Entity\Classes;
use ElearningBundle\Form\ClassesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Entity\Site;
use Doctrine\ORM\PersistentCollection;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Classes controller.
 *
 * @Route("admin/classes")
 */
class ClassesController extends Controller
{

    /**
     * Lists all Classes entities.
     *
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        
        if (!$user->isGranted('ROLE_ADMIN')) {
            
            $repository = $em->getRepository('ElearningBundle:Classes');
            $q = $repository->createQueryBuilder('c')
                ->select('c')
                ->leftJoin('c.course', 'course')
                ->leftJoin('course.site', 'site')
                ->where('site.id = :siteId')
                ->setParameter('siteId', $user->getSite()->getId())
                ->getQuery();

             $entities = $q->getResult();

             return array(
                'entities' => $entities,
            );
        }

        $entities = $em->getRepository('ElearningBundle:Classes')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Returns a list of Classes entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/list.{_format}", requirements={ "_format" = "json" }, defaults={ "_format" = "json" })
     * @Method("GET")
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('ElearningBundle:Classes'));
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $jsonList->setSiteId($user->getSite()->getId());
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Creates a new Classes entity.
     *
     * @Route("/")
     * @Method("POST")
     * @Template("ElearningBundle:Classes:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Classes();
        $form = $this->createNewForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // add the relationship between the class and the teacher
//            foreach ($entity->getStudents() as $student) {
//                $entity->addStudent($student);
//            }

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'classes.created');

            return $this->redirect($this->generateUrl('elearning_classes_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Classes entity.
    *
    * @param Classes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createNewForm(Classes $entity)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $formConfig = array(
            'action' => $this->generateUrl('elearning_classes_create'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        );
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['site'] = $user->getSite()->getId();
        }
        $form = $this->createForm('ElearningBundle\Form\ClassesType', $entity, $formConfig);

        return $form;
    }

    /**
     * Displays a form to create a new Classes entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Classes();
        $form   = $this->createNewForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Classes entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Classes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Classes entity.
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Classes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classes entity.');
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
    * Creates a form to edit a Classes entity.
    *
    * @param Classes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Classes $entity)
    {
        $formConfig = array(
            'action' => $this->generateUrl('elearning_classes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        );
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['site'] = $user->getSite()->getId();
        }
        $form = $this->createForm('\ElearningBundle\Form\ClassesType', $entity, $formConfig);
        return $form;
    }
    /**
     * Edits an existing Classes entity.
     *
     * @Route("/{id}")
     * @Method("PUT")
     * @Template("ElearningBundle:Classes:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElearningBundle:Classes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classes entity.');
        }

//        $originalStudents = new ArrayCollection();
//        // Create an ArrayCollection of the current Tag objects in the database
//        foreach ($entity->getStudents() as $student) {
//            $originalStudents->add($student);
//        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // add the relationship between the students and the classes
//            foreach ($entity->getStudents() as $student) {
//                if (false === $originalStudents->contains($student)) {
//                    $entity->addStudent($student);
//                }
//            }

             // remove the relationship between the class and the teacher
//            foreach ($originalStudents as $student) {
//                if (false === $entity->getStudents()->contains($student)) {
//                    // remove the class from the teacher
////                    $student->getTeachers()->removeElement($entity);
//                    // if it was a many-to-one relationship, remove the relationship like this
//                     $student->setClassesStudent(null);
//                    $em->persist($student);
//                    // if you wanted to delete the Tag entirely, you can also do that
//                    // $em->remove($class);
//                }
//            }
            
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'classes.updated');

            return $this->redirect($this->generateUrl('elearning_classes_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Classes entity.
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
            $entity = $em->getRepository('ElearningBundle:Classes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Classes entity.');
            }

            try {
                $em->remove($entity);
                $em->flush();
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un problema al intentar borrar el elemento, borra los valores relacionados primero y luego vuelve a intentarlo.');
                return $this->redirect($this->generateUrl('elearning_classes_edit', array('id' => $id)));
            }

        }

        return $this->redirect($this->generateUrl('elearning_classes_index'));
    }

    /**
     * Creates a form to delete a Classes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->setAction($this->generateUrl('elearning_classes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('id', HiddenType::class)
            ->getForm()
        ;
    }

    /**
     * @Route("/classes-remove" )
     * @Method("POST")
     */
    public function removeClassesAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $data = $this->get('request_stack')->getCurrentRequest()->request->all();

            $manager = $this->getDoctrine()->getManager();

            if (isset($data['id'])) {
                 $entity = $manager->getRepository('ElearningBundle:Classes')->findOneBy(array(
                        'id' => $data['id']
                        ));
                
                try {
                    $manager->remove($entity);
                    $manager->flush();
                } catch (\Exception $exc) {
                    $returnResponse = new stdClass();
                    $returnResponse->status = 'error';
                    $returnResponse->error = new stdClass();
                    $returnResponse->error->mesage = $exc->getMessage();

                    return new JsonResponse($returnResponse);
                }
            
            } else {
                throw new ExceptionBase('Index "id" not defined.');
            }

            $returnResponse = new stdClass();
            $returnResponse->status = 'success';

            return new JsonResponse($returnResponse);
        }
    }
}
