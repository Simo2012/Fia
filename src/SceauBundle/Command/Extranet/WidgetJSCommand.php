<?php

namespace SceauBundle\Command\Extranet;

use SceauBundle\Entity\QuestionnairePersonnalisation;
use SceauBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WidgetJSCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sceau:extranet:widget-js')
            ->setDescription('Generate JS files for each site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sites = $this->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('SceauBundle:Site')->questionnairePrincipal()
        ;

        array_map(function(Site $site) {
            if ($site->getQuestionnairePersonnalisations() && $site->getQuestionnairePersonnalisations()->count()) {
                /** @var QuestionnairePersonnalisation $questionnairePersonnalisation */
                $questionnairePersonnalisation = $site->getQuestionnairePersonnalisations()->first();
                if (($questionnaireType = $questionnairePersonnalisation->getQuestionnaireType())
                    && isset($questionnaireType->getParametrage()['commentairePrincipal'])) {

                    $questionnaires = $this->getContainer()->get('doctrine.orm.entity_manager')
                        ->getRepository('SceauBundle:Questionnaire')
                        ->commentairesPrincipaux($site, $questionnaireType)
                    ;

                    // Génération du widget blanc
                    $fp = fopen($this->getContainer()->getParameter('widget_path') . 'blanc/' . $site->getId() . '.js', 'w');
                    fwrite($fp, $this->getContainer()->get('templating')
                        ->render('@Sceau/Extranet/Widget/widgetcomm.js.twig', [
                            'site'           => $site,
                            'questionnaires' => $questionnaires,
                            'blanc'          => true,
                        ])
                    );
                    fclose($fp);

                    // Génération du widget transparent
                    $fp = fopen($this->getContainer()->getParameter('widget_path') . 'transparent/' . $site->getId() . '.js', 'w');
                    fwrite($fp, $this->getContainer()->get('templating')
                        ->render('@Sceau/Extranet/Widget/widgetcomm.js.twig', [
                            'site'           => $site,
                            'questionnaires' => $questionnaires,
                            'blanc'          => false,
                        ])
                    );
                    fclose($fp);
                }
            }
        }, $sites);
    }
}