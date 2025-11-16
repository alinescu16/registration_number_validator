<?php

namespace Alinandrei\RegistrationNumberValidator\Services;

use DragonBe\Vies\Vies;
use DragonBe\Vies\ViesException;
use DragonBe\Vies\ViesServiceException;


class VIESHeartBeatService
{
    public function isServiceAvailable(): bool
    {
        $vies = new Vies();

        return $vies->getHeartBeat()->isAlive();
    }
}