<?php
namespace SceauBundle\Command\Cron;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use SceauBundle\Exception\FluxException;
use SceauBundle\Service\GestionFlux;
use SceauBundle\Service\GestionQuestionnaire;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidationFluxCommand extends Command
{
    private $em;
    private $gestionFlux;
    private $gestionQuest;
    private $logger;

    public function __construct(
        ObjectManager $em,
        GestionFlux $gestionFlux,
        GestionQuestionnaire $gestionQuest,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->em = $em;
        $this->gestionFlux = $gestionFlux;
        $this->gestionQuest = $gestionQuest;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('sceau:cron:validation-flux')
            ->setDescription('Valide ou non les flux XML des marchands et génère un questionnaire pour chaque flux valide.')
            ->addArgument('nbMaxFlux', InputArgument::REQUIRED, 'Nombre maximum de flux traités à chaque exécution de la commande')
            ->setHelp(<<<EOT
<info>%command.name%</info> permet de valider ou non les flux XML des marchands et génère un questionnaire pour chaque flux valide.

Il est obligatoire de préciser le nombre maximum de flux que la commande doit traiter.

Exemple d'utilisation :

<info>php %command.full_name% 400</info>
EOT
            );
    }

    /**
     * @inheritdoc
     *
     * On récupère dans un premier temps uniquement les identifiants des flux car on doit faire ensuite un update sur
     * ces flux. Si on récupère directement les objets Flux grâce à un paginator, l'update "casse" le paginator.
     *
     * En cas d'erreur non attendues (catch d'une Exception), on remet le flux dans son statut initial pour qu'il soit
     * de nouveau traiter au prochain lancement de la commande.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nbMaxFlux = $input->getArgument('nbMaxFlux');
        $this->logger->info("DEBUT de la validation de $nbMaxFlux flux");

        if (is_numeric($nbMaxFlux)) {
            $listeIdFlux = $this->em->getRepository('SceauBundle:Flux')->listeIdFluxNonTraites($nbMaxFlux);

            if (!empty($listeIdFlux)) {
                $fluxStatutATraiter = $this->em->getRepository('SceauBundle:FluxStatut')->aTraiter();
                $fluxStatutValide = $this->em->getRepository('SceauBundle:FluxStatut')->traiteEtValide();
                $fluxStatutNonValide = $this->em->getRepository('SceauBundle:FluxStatut')->traiteEtInvalide();

                $this->em->getRepository('SceauBundle:Flux')->updateEnCoursDeTraitement($listeIdFlux);
                $fluxNonTraites = $this->em->getRepository('SceauBundle:Flux')->listeFluxParId($listeIdFlux);

                foreach ($fluxNonTraites as $fluxNonTraite) {
                    try {
                        $this->gestionFlux->validerContenu($fluxNonTraite);
                        $this->gestionQuest->genererQuestionnaireViaFlux($fluxNonTraite);

                        $fluxNonTraite->setFluxStatut($fluxStatutValide);

                        $this->logger->info(
                            sprintf(
                                'Le flux %d du site %d est valide',
                                $fluxNonTraite->getId(),
                                $fluxNonTraite->getSite()->getId()
                            )
                        );

                    } catch (FluxException $e) {
                        $fluxNonTraite->setFluxStatut($fluxStatutNonValide);
                        $fluxNonTraite->setLibelleErreur($e->getMessage());

                        $this->logger->warning(
                            sprintf(
                                'Le flux %d du site %d n\'est pas valide : %s',
                                $fluxNonTraite->getId(),
                                $fluxNonTraite->getSite()->getId(),
                                $e->getMessage()
                            )
                        );

                    } catch (Exception $e) {
                        $fluxNonTraite->setFluxStatut($fluxStatutATraiter);

                        $this->logger->error(
                            sprintf(
                                'Erreur lors de la validation du flux %d du site %d : %s',
                                $fluxNonTraite->getId(),
                                $fluxNonTraite->getSite()->getId(),
                                $e->getMessage()
                            )
                        );
                    }
                }
                $this->em->flush();

                $output->writeln('<info>La validation est terminée !</info>');

            } else {
                $output->writeln('<info>Il n\'y a aucun flux à traiter.</info>');
            }

        } else {
            $output->writeln('<error>Le nombre maximum de flux doit etre un entier.</error>');
            $this->logger->error("Le nombre maximum de flux passé à la commande est incorrect");
        }

        $this->logger->info("FIN de la validation des flux");
    }
}
