<?php

namespace SceauBundle\Cache;

class Cache
{
    const LIFETIME_5M = 300;
    const LIFETIME_15M = 900;
    const LIFETIME_1J = 86400;

    /**
     * Retourne le nombre de seconde avant minuit.
     *
     * @return int
     */
    public static function lifetimeUntilMidnight()
    {
        return strtotime("tomorrow 00:00:00") - time();
    }
}
