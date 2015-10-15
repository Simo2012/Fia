<?php

namespace SceauBundle\Command\Extranet;

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
        //TODO : filter on site.type
        $sites = $this->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('SceauBundle:Site')->findAll();

        array_map(function(Site $site) {
            $fp = fopen($this->getContainer()->getParameter('widget_path') . $site->getId() . '.js', 'w');
            fwrite($fp, $this->getContainer()->get('templating')->render('@Sceau/Extranet/Widget/widgetcomm.js.twig', [
                'site' => $site,
                'comments' => [],
            ]));
            fclose($fp);
        }, $sites);
    }
}