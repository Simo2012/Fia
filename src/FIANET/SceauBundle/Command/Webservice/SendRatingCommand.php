<?php
namespace FIANET\SceauBundle\Command\Webservice;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendRatingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fianet:webservice:send-rating:test')
            ->setDescription('Permet de simuler l\'envoi d\'un flux XML pour un site')
            ->addArgument('site_id', InputArgument::REQUIRED, 'Identifiant du site')
            ->addArgument('xml', InputArgument::REQUIRED, 'Flux XML de la commande')
            ->addOption('checksum', null, InputOption::VALUE_REQUIRED, 'Hash MD5 du XML. Si non definie, le checksum sera genere automatiquement.')
            ->setHelp(<<<EOT
<info>fianet:webservice:send-rating:test</info> permet de simuler l'envoi d'un flux XML par un site.

Il est obligatoire de preciser l'identifiant du site et le flux XML. Optionnelement, il est possible de donner directement le checksum. S'il est absent, la commande le genere automatiquement.

Exemple d'utilisation :

<info>php app/console fianet:webservice:send-rating:test 6607 "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><control>...</control>"</info>

ou

<info>php app/console fianet:webservice:send-rating:test 6607 "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><control>...</control>" --checksum=78b8ee77dc6ac513a1c68ef5c06185a6</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site_id = $input->getArgument('site_id');
        $xml = $input->getArgument('xml');
        $checksum = $input->getOption('checksum');

        $checksumGenere = md5($xml);

        if (!$checksum) {
            /* Checksum absent : on prend celui que l'on vient de générer */
            $checksum = $checksumGenere;

        } elseif ($checksum != $checksumGenere) {
            /* Checksum présent mais invalide : on retourne un message d'erreur */
            $output->writeln('<error>Le checksum indique est incorrect.</error>');

            return 1;
        }

        $ch = curl_init($this->getContainer()->get('router')->generate('ws_send_rating', array(), true));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        $post = array(
            "SiteID" => $site_id,
            "XMLInfo" => $xml,
            "CheckSum" => $checksum
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        $response = curl_exec($ch);
        if (!curl_errno($ch)) {
            $output->writeln("<info>$response</info>");

        } else {
            $output->writeln('<error>' . curl_error($ch) . '</error>');
        }

        return 0;
    }
}