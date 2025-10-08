<?php

namespace Alinandrei\RegistrationNumberValidator\Tests;

use Alinandrei\RegistrationNumberValidator\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
