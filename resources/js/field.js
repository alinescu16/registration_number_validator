import RegistrationNumberValidator from './components/fieldtypes/RegistrationNumberValidator.vue';
import { createApp } from 'vue';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('registration-number-validator-container');

    if ( ! el ) {
        return;
    }

    const props = JSON.parse(el.dataset.config);

    const app = createApp(RegistrationNumberValidator, props);

    app.mount(el);
});