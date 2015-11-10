<?php

namespace SceauBundle\Command\Cron;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\NoResultException;
use SceauBundle\Service\EnvoieQuestionnaire;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EnvoieQuestionnairesCommand extends Command
{
    private $em;
    private $eq;

    public function __construct(ObjectManager $em, EnvoieQuestionnaire $eq)
    {
        parent::__construct();

        $this->em = $em;
        $this->eq = $eq;
    }

    protected function configure()
    {
        $this
            ->setName('sceau:cron:envoie-quest')
            ->setDescription('Envoie les e-mails contenant les liens vers les questionnaires.')
            ->addArgument('nb', InputArgument::REQUIRED, 'Nombre de questionnaires envoyés par exécution')
            ->addOption('qtype_id', null, InputOption::VALUE_REQUIRED, 'Si cette option est définie, seuls les questionnaires du type indiqué seront envoyés.')
            ->setHelp(<<<EOT
<info>%command.name%</info> permet d'envoyer les e-mails contenant les liens vers les questionnaires.
Si un type de questionnaire est passé en option, seuls les questionnaires de ce type seront envoyés.

Exemple d'utilisation :

<info>php %command.full_name% 400</info>
ou
<info>php %command.full_name% 400 --qtype_id=3</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nbQuestionnaire = $input->getArgument('nb');
        $questionnaireType_id = $input->getOption('qtype_id');

        if (is_numeric($nbQuestionnaire)) {
            if ($questionnaireType_id === null || is_numeric($questionnaireType_id)) {
                if ($questionnaireType_id !== null) {
                    try {
                        $questionnaireType = $this->em->getRepository('SceauBundle:QuestionnaireType')
                            ->get($questionnaireType_id);

                    } catch (NoResultException $e) {
                        $output->writeln('<error>Le type de questionnaire indiqué n\'existe pas</error>');
                        return 3;
                    }

                } else {
                    $questionnaireType = null;
                }

                try {
                    $this->eq->envoyer($nbQuestionnaire, $questionnaireType);
                    $output->writeln('<info>L\'envoi des questionnaires est terminé !</info>');

                } catch (\Exception $e) {
                    $output->writeln('<error>'. $e->getMessage() .'</error>');
                    return 4;
                }

            } else {
                $output->writeln('<error>Merci d\'indiquer un entier pour le type de questionnaire !</error>');
                return 2;
            }
        } else {
            $output->writeln('<error>Merci d\'indiquer un entier pour le nombre maximum d\'e-mail envoyé !</error>');
            return 1;
        }

        return 0;
    }
}
