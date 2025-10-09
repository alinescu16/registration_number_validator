import RegistrationNumberValidator from './components/fieldtypes/RegistrationNumberValidator.vue';

Statamic.booting(() => {
    Statamic.$components.register('registration-number-validator', RegistrationNumberValidator);
});