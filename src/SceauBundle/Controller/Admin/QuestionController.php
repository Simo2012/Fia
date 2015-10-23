<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SceauBundle\Form\Type\Admin\TicketReponseType;
/**
 * QuestionController controller.
 *
 * @Route("/questions")
 */
class QuestionController extends Controller
{

    /**
     * Questions.
     *
     * @Route("/", name="questions")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        // $entityRepo = $this->get('sceau.repository.question');
        // $entities = $entityRepo->findBy(array(), array('date' => 'ASC'));

        return array(
            'entities' => [1,2,3,4]
        );
    }


    /**
     * Finds and displays a InternauteQuestion entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function showAction($id)
    {
        $formTicketReponse = $this->createForm(new TicketReponseType());

        $request = $this->get('request');
        if($request->isXmlHttpRequest()) {  
            $modeleType = $request->request->get('modeleType');  
            var_dump($modeleType);          
            //
            // Ici ajax renvoit l'indice de l'ufr selectionné parce-que les ufrs sont affichés
            // dans la liste sans être triés On récupère l'ufr pour l'index récupéré puis on
            // récupère les diplomes pour cet ufr. On le met dans la variable $mesDiplomes.
            //
            // $em = $this->getDoctrine()
            //            ->getEntityManager();    
                        
            // $ufrChoisi = $em->getRepository('UdcDiplomeBundle:Ufr')
            //                 ->find($idUFR);
            // $this->container->get('request')->getSession()->set('ufrSelected', $ufrChoisi->getId());            
            // $diplomesDeLufrChoisi = $ufrChoisi->getDiplomes();          
                 
            // $html = "<select id=\"udc_diplomebundle_etudianttype_diplome\" name=\"udc_diplomebundle_etudianttype[diplome]\" required=\"required\" class=\"span4\">";
            // foreach ($diplomesDeLufrChoisi as $dip) {
            //     $html = $html . "<option value=\"" . $dip->getId() . "\">" . $dip->getNomDiplome() . "</option>";
            // }          
            // $html = $html . "</select>";             
            // return new Response($html);
             
        }
        

        return array(
            'entity'            => [],
            'formTicketReponse' => $formTicketReponse->createView(),
        );
    }

    public function updateReponseFromSelect()
    {
        
    }
}
