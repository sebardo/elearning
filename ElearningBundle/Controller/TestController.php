<?php

namespace ElearningBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ElearningBundle\Entity\Test;
use ElearningBundle\Form\TestType;
use ElearningBundle\Form\AskType;
use ElearningBundle\Form\TestAskType;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use ElearningBundle\Entity\Ask;
use ElearningBundle\Entity\Answer;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
/**
 * Test controller.
 *
 * @Route("/admin/test")
 */
class TestController extends Controller
{

    /**
     * Lists all Test entities.
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
     * Returns a list of Test entities in JSON format.
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
        $jsonList->setRepository($em->getRepository('ElearningBundle:Test'));
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $siteIds = $adminManager->getSiteIds(); 
            $jsonList->setSiteIds($siteIds);
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Returns a list of Test entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/list-actor-test/{test}", name="core_test_actorlist")
     * @Method("GET")
     */
    public function actorListAction($test)
    {
        $em = $this->getDoctrine()->getManager();

        $adminManager = $this->get('admin_manager');
        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('ElearningBundle:ActorTest'));
        
        $jsonList->setEntityId($test);
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Creates a new Test entity.
     *
     * @Route("/")
     * @Method("POST")
     * @Template("ElearningBundle:Test:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Test();
        $form = $this->createNewForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'test.created');

            //add notification
//            if(!is_null($entity->getSite())){
//                //create task
//                $user =  $this->get('security.token_storage')->getToken()->getUser();
//                $task = new Task();
//                $task->setIsActive(true);
//                $task->setName('Add Test notification');
//                $task->setFunctionName('addNotifications');
//                $task->setParams(json_encode(array('site' => $entity->getSite()->getId(), 'type' => 'test')));
//                $task->setActor($user);
//                $em->persist($task);
//                $em->flush();
//            }
            
            return $this->redirect($this->generateUrl('elearning_test_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Test entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Test();
        $form   = $this->createNewForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Test entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Test $test)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($test);

        return array(
            'entity'      => $test,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Test entity.
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Test $test)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($test);
        $deleteForm = $this->createDeleteForm($test);
        $askForm = $this->createForm('ElearningBundle\Form\TestAskType', $test, array('test' => $test));
        
        try {
            $nextAnswer = $em->getRepository('ElearningBundle:Answer')->getNextId();
        } catch (\Exception $exc) {
             $nextAnswer = 1;
        }

        
                
        return array(
            'entity'      => $test,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'asks_form'   => $askForm->createView(),
            'next_answer' => $nextAnswer
        );
    }

    
    /**
    * Creates a form to create a Test entity.
    *
    * @param Test $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    public function createNewForm(Test $entity)
    {
        $formConfig = array(
            'action' => $this->generateUrl('elearning_test_create'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ); 
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['siteIds'] = $this->get('admin_manager')->getSiteIds(); 
        }
        $form = $this->createForm('\ElearningBundle\Form\TestType',$entity, $formConfig);
        return $form;
    }

    /**
    * Creates a form to edit a Test entity.
    *
    * @param Test $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Test $entity)
    {
        $formConfig = array(
            'action' => $this->generateUrl('elearning_test_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ); 
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['siteIds'] = $this->get('admin_manager')->getSiteIds(); 
        }
        $form = $this->createForm('ElearningBundle\Form\TestType', $entity, $formConfig);
        return $form;
    }
    /**
     * Edits an existing Test entity.
     *
     * @Route("/{id}")
     * @Method("PUT")
     * @Template("ElearningBundle:Test:edit.html.twig")
     */
    public function updateAction(Request $request, Test $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $askForm = $this->createForm('\ElearningBundle\Form\TestAskType', $entity);
        
        $redirectParams = array('id' => $entity->getId());

        $arrValues = array();
        foreach ($entity->getAsks() as $value) {
            $arrValues[] = $value->getId();
        }

        if ($request->request->has('test_ask')) {
            $data = $request->request->get('test_ask');
            foreach ($data['asks'] as $ask) {
                //update exist value
                if(in_array($ask, $arrValues)){
                    $askEntity = $em->getRepository('ElearningBundle:Ask')->find($ask);
                    
                    //answers
                    if($askEntity instanceof Ask){
                        //remove entity if vales are empty
                        $answers = $request->request->get($ask.'_answer');
                        $removeItems = array();
                        foreach( $answers as $key => $value ){
                            if( empty( $value ) ) {
                                $removeItems[] = $key;
                               unset( $answers[$key] );
                            }
                        }
                        //if all answer are clean and empty remove ask from test
                        if( empty( $answers ) ) {
                            $em->remove($askEntity);
                           
                        }else{
                            //if not all answer are clean and empty 
                            foreach ($removeItems as $item) {
                                $ansEntity = $em->getRepository('ElearningBundle:Answer')->find($item);
                                $em->remove($ansEntity);
                            }
                        }
                        //create new Answer
                        foreach ($answers as $key => $answer) {
                            $ansEntity = $em->getRepository('ElearningBundle:Answer')->find($key);
                            if($ansEntity instanceof Answer){
                                $ansEntity->setName($answer);
                            }else{
                                $ans = new Answer();
                                $ans->setName($answer);
                                $ans->setAsk($askEntity);
                                $em->persist($ans);
                            } 
                           
                        }
                         $em->flush();
                    }
                    
                    //correct
                    if($request->request->has($ask.'_correct')){
                        $answerCorrect = $request->request->get($ask.'_correct');
                        
                        foreach ($askEntity->getAnswers() as $answer) {
                            if($answer->getId() == $answerCorrect){
                                $answer->setCorrect(true);
                            }else{
                                $answer->setCorrect(false);
                            }
                        }
                        $em->flush();
                    }
                    
                }else{
                    //add new value
                }
                
            }
            
            //when ask removed
            foreach ($arrValues as $askExisting) {
                //update exist value
                if(in_array($askExisting, $data['asks'])){
                }else{
                    $askEntity = $em->getRepository('ElearningBundle:Ask')->find($askExisting);
                    if($askEntity instanceof Ask){
                        $em->remove($askEntity);
                    }
                }
            }
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'test.updated');

            return $this->redirect($this->generateUrl('elearning_test_show', array_merge($redirectParams, array('ask' => 1))));
                
        }else{
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'test.updated');
                return $this->redirect($this->generateUrl('elearning_test_show', $redirectParams));
            }else{
              $string = var_export($this->getErrorMessages($editForm), true);
              print_r($string);die();
            }
        }
        

        $nextAnswer = $em->getRepository('ElearningBundle:Answer')->getNextId();
        
        print_r($nextAnswer);die();

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ask_form'    => $askForm->createView()
        );
    }
    
    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
    $errors = array();

    foreach ($form->getErrors() as $key => $error) {
        if ($form->isRoot()) {
            $errors['#'][] = $error->getMessage();
        } else {
            $errors[] = $error->getMessage();
        }
    }

    foreach ($form->all() as $child) {
        if (!$child->isValid()) {
            $errors[$child->getName()] = $this->getErrorMessages($child);
        }
    }

    return $errors;
}

    /**
     * Deletes a Test entity.
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
            $entity = $em->getRepository('ElearningBundle:Test')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Test entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('danger', 'test.deleted');
        }

        return $this->redirect($this->generateUrl('elearning_test_index'));
    }

    /**
     * Creates a form to delete a Test entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Test $test)
    {
        return $this->createFormBuilder(array('id' => $test->getId()))
            ->setAction($this->generateUrl('elearning_test_delete', array('id' => $test->getId())))
            ->setMethod('DELETE')
            ->add('id', HiddenType::class)
            ->getForm()
        ;
    }

}
