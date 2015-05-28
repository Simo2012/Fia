<?php
namespace FIANET\SceauBundle\Command\Cron;

use FIANET\SceauBundle\Exception\FluxException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidationFluxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fianet:cron:validation-flux')
            ->setDescription('Valide ou non les flux XML des marchands et genere un questionnaire si la reponse est positive.')
            ->addArgument('nbMaxFlux', InputArgument::REQUIRED, 'Nombre maximum de flux traites a chaque execution de la commande')
            ->setHelp(<<<EOT
<info>fianet:cron:validation-flux</info> permet de valider ou ou non les flux XML des marchands et genere un questionnaire si la reponse est positive.

Il est obligatoire de preciser le nombre maximum de flux que la commande doit traiter.

Exemple d'utilisation :

<info>php app/console fianet:cron:validation-flux 400</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nbMaxFlux = $input->getArgument('nbMaxFlux');

        if (!is_numeric($nbMaxFlux)) {
            $output->writeln('<error>Le nombre maximum de flux doit etre un entier.</error>');

            return 1;
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $fluxNonTraites = $em->getRepository('FIANETSceauBundle:Flux')->fluxNonTraites($nbMaxFlux);

        if (!empty($fluxNonTraites)) {
            $fluxStatutEnCours = $em->getRepository('FIANETSceauBundle:FluxStatut')->enCoursDeTraitement();
            $fluxStatutValide = $em->getRepository('FIANETSceauBundle:FluxStatut')->traiteEtValide();
            $fluxStatutNonValide = $em->getRepository('FIANETSceauBundle:FluxStatut')->traiteEtInvalide();

            /* On change le statut des flux -> en cours de traitement */
            foreach ($fluxNonTraites as $fluxNonTraite) {
                $fluxNonTraite->setFluxStatut($fluxStatutEnCours);
            }
            $em->flush();

            /* On lance la validation du contenu des flux */
            $gestionFlux = $this->getContainer()->get('fianet_sceau.flux');

            foreach ($fluxNonTraites as $fluxNonTraite) {
                try {
                    $gestionFlux->validerContenu($fluxNonTraite);

                    $fluxNonTraite->setFluxStatut($fluxStatutValide);

                } catch (FluxException $e) {
                    $fluxNonTraite->setFluxStatut($fluxStatutNonValide);
                    $fluxNonTraite->setLibelleErreur($e->getMessage());
                }
            }
            $em->flush();

            return 0;

        } else {
            $output->writeln('<info>Il n\'y a aucun flux à traiter.</info>');

            return 0;
        }
    }
}