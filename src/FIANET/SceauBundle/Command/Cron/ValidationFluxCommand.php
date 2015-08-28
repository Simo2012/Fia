<?php
namespace FIANET\SceauBundle\Command\Cron;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use FIANET\SceauBundle\Exception\FluxException;
use FIANET\SceauBundle\Service\GestionFlux;
use FIANET\SceauBundle\Service\GestionQuestionnaire;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidationFluxCommand extends Command
{
    private $em;
    private $gf;
    private $gq;

    public function __construct(ObjectManager $em, GestionFlux $gf, GestionQuestionnaire $gq)
    {
        parent::__construct();

        $this->em = $em;
        $this->gf = $gf;
        $this->gq = $gq;
    }

    protected function configure()
    {
        $this
            ->setName('fianet:cron:validation-flux')
            ->setDescription('Valide ou non les flux XML des marchands et génère un questionnaire pour chaque flux valide.')
            ->addArgument('nbMaxFlux', InputArgument::REQUIRED, 'Nombre maximum de flux traités à chaque exécution de la commande')
            ->setHelp(<<<EOT
<info>%command.name%</info> permet de valider ou non les flux XML des marchands et génère un questionnaire pour chaque flux valide.

Il est obligatoire de preciser le nombre maximum de flux que la commande doit traiter.

Exemple d'utilisation :

<info>php %command.full_name% 400</info>
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

        $fluxNonTraites = $this->em->getRepository('FIANETSceauBundle:Flux')->fluxNonTraites($nbMaxFlux);

        if (!empty($fluxNonTraites)) {
            $fluxStatutATraiter = $this->em->getRepository('FIANETSceauBundle:FluxStatut')->aTraiter();
            $fluxStatutEnCours = $this->em->getRepository('FIANETSceauBundle:FluxStatut')->enCoursDeTraitement();
            $fluxStatutValide = $this->em->getRepository('FIANETSceauBundle:FluxStatut')->traiteEtValide();
            $fluxStatutNonValide = $this->em->getRepository('FIANETSceauBundle:FluxStatut')->traiteEtInvalide();

            /* On change le statut des flux -> en cours de traitement */
            foreach ($fluxNonTraites as $fluxNonTraite) {
                $fluxNonTraite->setFluxStatut($fluxStatutEnCours);
            }
            $this->em->flush();

            /* On lance la validation du contenu des flux */
            foreach ($fluxNonTraites as $fluxNonTraite) {
                try {
                    $this->gf->validerContenu($fluxNonTraite);
                    $this->gq->genererQuestionnaireViaFlux($fluxNonTraite);

                    $fluxNonTraite->setFluxStatut($fluxStatutValide);

                } catch (FluxException $e) {
                    $fluxNonTraite->setFluxStatut($fluxStatutNonValide);
                    $fluxNonTraite->setLibelleErreur($e->getMessage());

                } catch (Exception $e) {
                    $fluxNonTraite->setFluxStatut($fluxStatutATraiter);
                    $output->writeln('<error>La validation du flux ' . $fluxNonTraite->getId() .
                        ' a échoué : ' . $e->getMessage() . '</error>');
                }
            }
            $this->em->flush();

            $output->writeln('<info>La validation est terminée !</info>');

            return 0;

        } else {
            $output->writeln('<info>Il n\'y a aucun flux à traiter.</info>');

            return 0;
        }
    }
}
