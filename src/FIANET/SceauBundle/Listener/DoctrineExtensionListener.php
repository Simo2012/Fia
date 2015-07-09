<?php
namespace FIANET\SceauBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ecouteur d'évenement permettant de stocker la locale de la requête (Request).
 * Dans le cas où la requête fait appel à Doctrine et que le DQL contient du texte à traduire, cette locale va
 * permettre à l'extension translatable de traduire le texte dans la bonne langue.
 *
 * @link https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/symfony2.md#doctrine-extension-listener-services
 */
class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onLateKernelRequest(GetResponseEvent $event)
    {
        $translatable = $this->container->get('gedmo.listener.translatable');
        $translatable->setTranslatableLocale($event->getRequest()->getLocale());
    }
}
