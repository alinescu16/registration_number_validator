<?php

namespace Alinandrei\RegistrationNumberValidator\Widgets;

use Statamic\Widgets\Widget;

use Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService;

class RegistrationNumberValidatorWidget extends Widget
{
    protected static $title = 'VIES Heartbeat';

    public function html()
    {
        $viesServiceStatus = $this->getVIESHeartBeat() ? 'Available' : 'Unavailable';
        $viesServiceStatusClass = $viesServiceStatus == 'Available' ? 'bg-lime-500' : 'bg-rose-500';

        $data = [
            'status' => $viesServiceStatus,
            'status_class' => $viesServiceStatusClass,
        ];

        return view('registration_number_validator::vies_heartbeat_status_widget', array_merge($this->config, $data));
    }

    /**
     * Check if the VIES service is available.
     *
     * @return bool
     */
    private function getVIESHeartBeat(): bool
    {
        $viesService = app(VIESHeartBeatService::class);

        return $viesService->isServiceAvailable();
    }
}