<?php

namespace SceauBundle\Command\Mail;

use SceauBundle\Entity\EnvoiEmail;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mails:send')
            ->setDescription('Sending mails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $envoiEmailRepository = $this->getContainer()->get('sceau.repository.envoi.email');
        /* TODO : il ne faut pas tout sélectionner : seulement les e-mails qui n'ont pas encore été envoyé
        et le faire par paquet */
        $mails = $envoiEmailRepository->findAll();

        $mailer = $this->getContainer()->get('mailer');

        foreach ($mails as $mail) {
            $message = \Swift_Message::newInstance()
                ->setFrom($mail->getSendFrom())
                ->setTo($mail->getSendTo())
                ->setSubject($mail->getSubject())
                ->setCharset('utf8')
                ->setBody($mail->getContent())
            ;
            $mailer->send($message);

            $mail->setStatus(EnvoiEmail::SUCCESS);
            $em->persist($mail);
            $em->flush();
        }
    }
}
