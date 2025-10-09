import RegistrationNumberValidator from './components/fieldtypes/RegistrationNumberValidator.vue';

Statamic.booting(() => {
    console.log("Addon Registration Number Validator loaded - JS");

    Statamic.$components.register('registration-number-validator', RegistrationNumberValidator);
});