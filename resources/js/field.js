import RegistrationNumberValidator from './components/fieldtypes/RegistrationNumberValidator.vue';
import { createApp } from 'vue';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('registration-number-validator-container');

    if ( ! el ) {
        return;
    }

    const app = createApp(RegistrationNumberValidator)

    app.mount(el);
});