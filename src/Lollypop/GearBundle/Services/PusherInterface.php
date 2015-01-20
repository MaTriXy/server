<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PusherInterface
 *
 * @author seshachalam
 */
interface PusherInterface
{

    public function setGoogleApiKey($googleApiKey);

    public function getGoogleApiKey();

    public function broadcast(array $receivers, $data, $collapseKey,
                              $delayWhileIdle, $ttl, $packageName,
                              $dryRun = false);

}
