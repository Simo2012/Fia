<?php
namespace SceauBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Souscripteur d'évenement permettant de stocker la locale en session.
 * Si la locale dans la requête n'existe pas, la locale stockée en session est utilisée.
 *
 * @link http://symfony.com/fr/doc/current/cookbook/session/locale_sticky_session.html
 */
class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        /* On essaie de voir si la locale a été fixée dans le paramètre de routing _locale */
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);

        } else {
            /* Si aucune locale n'a été fixée explicitement dans la requête, on utilise celle de la session */
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            /* Doit être enregistré avant le Locale listener par défaut */
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
