<?php
namespace SceauBundle\Command\Cron;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Service\ImportCSV;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChargementCSVCommand extends Command
{
    private $em;
    private $importCSV;

    public function __construct(ObjectManager $em, ImportCSV $importCSV)
    {
        parent::__construct();

        $this->em = $em;
        $this->importCSV = $importCSV;
    }

    protected function configure()
    {
        $this
            ->setName('sceau:cron:import-csv')
            ->setDescription('Importe les fichiers CSV d\'un marchand pour creer des questionnaires.')
            ->addArgument('site_id', InputArgument::REQUIRED, 'L\'identifiant du site')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Permet de simuler l\'import : aucune ligne n\'est insérée en base et les fichiers ne sont pas déplacés')
            ->setHelp(<<<EOT
<info>%command.name%</info> permet d'importer les fichiers CSV d'un site et génère un questionnaire pour chaque ligne correcte.

Exemple d'utilisation :

<info>php %command.full_name% 3267</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site_id = $input->getArgument('site_id');

        if (is_numeric($site_id)) {
            $site = $this->em->getRepository('SceauBundle:Site')->parametragesCSV($site_id);

            if ($site) {
                foreach ($site->getQuestionnairePersonnalisations() as $questionnairePersonnalisation) {
                    $this->importCSV->importerFichiers(
                        $site,
                        $questionnairePersonnalisation,
                        $input->getOption('debug') !== false ? true : false
                    );
                }

                $output->writeln('<info>L\'import est terminé !</info>');

            } else {
                $output->writeln('<error>Ce site n\'est pas autorisé à importer des fichiers CSV !</error>');
            }

        } else {
            $output->writeln('<error>Merci d\'indiquer un identifiant de site valide !</error>');
        }
    }
}
