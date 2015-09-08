<?php
namespace SceauBundle\Command\Webservice;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Exception\FluxException;
use SceauBundle\Service\GestionFlux;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

class SendRatingCommand extends Command
{
    private $em;
    private $router;
    private $gestionFlux;

    public function __construct(ObjectManager $em, RouterInterface $router, GestionFlux $gestionFlux)
    {
        parent::__construct();

        $this->em = $em;
        $this->router = $router;
        $this->gestionFlux = $gestionFlux;
    }

    protected function configure()
    {
        $this
            ->setName('sceau:webservice:send-rating:test')
            ->setDescription('Permet de simuler l\'envoi d\'un flux XML pour un site')
            ->addArgument('site_id', InputArgument::REQUIRED, 'Identifiant du site')
            ->setHelp(<<<EOT
<info>%command.name%</info> permet de simuler l'envoi d'un flux XML par un site.

Il est obligatoire de préciser l'identifiant d'un site existant. Les données du flux sont générées de manière pseudo-aléatoire.

Exemple d'utilisation :

<info>php %command.full_name% 6607
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site_id = $input->getArgument('site_id');
        if (is_numeric($site_id)) {
            $site = $this->em->getRepository('SceauBundle:Site')->find($site_id);

            if ($site) {
                $now = new \DateTime();
                $timestamp = $now->format('Y-m-d H:i:s');
                $email = 'robert.tenaif' . rand() . '@fia-net.com';
                $refid = uniqid('', true);
                $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
                $montant = rand(1, 10000);
                $crypt = $this->gestionFlux->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

                $xmlInfo = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><control><utilisateur>
                    <nom titre=\"1\">Tenaif</nom><prenom>Robert</prenom><email>$email</email></utilisateur>
                    <infocommande><siteid>$site_id</siteid><refid>$refid</refid>
                    <montant devise=\"EUR\">$montant</montant><ip timestamp=\"$timestamp\">$ip</ip>
                    </infocommande><crypt>$crypt</crypt></control>";
                $checksum = md5($xmlInfo);

                $output->writeln("<info>XML :</info>\n$xmlInfo\n");
                $output->writeln("<info>checksum :</info>\n$checksum\n");

                try {
                    $this->gestionFlux->inserer($site->getId(), $xmlInfo, $checksum, $ip);
                    $output->writeln('<info>Un flux a été inséré pour le site ' . $site->getNom() . '</info>');

                } catch (FluxException $e) {
                    $output->writeln('<error>Erreur : ' . $e->getMessage() . '</error>');
                }

            } else {
                $output->writeln('<error>Ce site n\'existe pas.</error>');
            }

        } else {
            $output->writeln('<error>Merci d\'indiquer un identifiant de site valide !</error>');
        }
    }
}
