<?php
namespace SceauBundle\Command\Cron;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
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
            ->setName('sceau:cron:validation-flux')
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

    /**
     * @inheritdoc
     *
     * On récupère dans un premier temps uniquement les identifiants des flux car on doit faire ensuite un update sur
     * ces flux. Si on récupère directement les objets Flux grâce à un paginator, l'update "casse" le paginator.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nbMaxFlux = $input->getArgument('nbMaxFlux');

        if (!is_numeric($nbMaxFlux)) {
            $output->writeln('<error>Le nombre maximum de flux doit etre un entier.</error>');

            return 1;
        }

        $listeIdFlux = $this->em->getRepository('SceauBundle:Flux')->listeIdFluxNonTraites($nbMaxFlux);

        if (!empty($listeIdFlux)) {
            $fluxStatutATraiter = $this->em->getRepository('SceauBundle:FluxStatut')->aTraiter();
            $fluxStatutValide = $this->em->getRepository('SceauBundle:FluxStatut')->traiteEtValide();
            $fluxStatutNonValide = $this->em->getRepository('SceauBundle:FluxStatut')->traiteEtInvalide();

            $this->em->getRepository('SceauBundle:Flux')->updateEnCoursDeTraitement($listeIdFlux);
            $fluxNonTraites = $this->em->getRepository('SceauBundle:Flux')->listeFluxParId($listeIdFlux);

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
