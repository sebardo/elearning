<?php

namespace ElearningBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use ElearningBundle\Entity\ActorTest;
use ElearningBundle\Entity\Test;
use DateTime;

/**
 * FrontController controller.
 */
class FrontController extends Controller
{

    /**
     * @Route("/elearning")
     * @Template()
     */
    public function elearningAction() {

        $em = $this->getDoctrine()->getManager();
        $query = ' SELECT t'
                . ' FROM ElearningBundle:Test t'
                . ' JOIN t.course c '
                ;
        $q = $em->createQuery($query);
        $tests = $q->getResult();

         //switch off notification message
//        $user = $this->container->get('security.token_storage')->getToken()->getUser();
//        $this->get('notification_manager')->disableNotification($user, 'test', $siteEntity);
        
        return $this->render('ElearningBundle:Front:elearning.html.twig', array(
            'tests' => $tests,
        ));
    }
    
    /**
     * @Route("/elearning/test/{test}")
     * @Template()
     */
    public function testAction(Test $test) {

        $em = $this->getDoctrine()->getManager();
        $actor = $this->get('security.token_storage')->getToken()->getUser();    
        $actorTest = $em->getRepository('ElearningBundle:ActorTest')->findOneBy(array(
                    'actor' => $actor, 
                    'test' => $test
                        ));
        
        $returnValues = array();
        if($actorTest instanceof ActorTest){
            $results = json_decode($actorTest->getData());
            
            foreach ( $results as $key => $value ){
                $returnValues[$key] = $value;
            }
        }
        
        
        return $this->render('ElearningBundle:Front:test.html.twig', array(
            'test' => $test,
            'returnValues' => $returnValues
        ));
    }
    
    /**
     * @Route("/formacion/evaluate")
     */
    public function evaluateAction(Request $request) {

        if($request->getMethod() == 'POST' && $request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $returnValues = array();
            $data = $request->request->all();
            $test = $em->getRepository('ElearningBundle:Test')->findOneBy(array('id' => $data['test'] ));
            $actor = $this->get('security.token_storage')->getToken()->getUser();
            foreach ($data['asks'] as $key => $value) {
                $askEntity = $em->getRepository('ElearningBundle:Ask')->findOneBy(array('id' => $key));
                $correctAnswer = $em->getRepository('ElearningBundle:Answer')->findOneBy(array(
                    'ask' => $askEntity, 
                    'correct' => 1
                        ));
                $returnValues[$key] = array('correct' => $correctAnswer->getId(), 'selected' => $value);
            }
            
            $actorTest = $em->getRepository('ElearningBundle:ActorTest')->findOneBy(array(
                    'actor' => $actor, 
                    'test' => $test
                        ));
            if(!$actorTest instanceof ActorTest){
                $item = new ActorTest();
                $item->setEvaluateDate(new DateTime());
                $item->setActor($actor);
                $item->setTest($test);
                $item->setData(json_encode($returnValues));
                $em->persist($item);
                $em->flush();
            }else{
                return new JsonResponse('Test already completed by Actor');
            }
            
            return new JsonResponse($returnValues);
        }

    }
    
}
