import RegistrationNumberValidator from './components/fieldtypes/RegistrationNumberValidator.vue';
import { createApp } from 'vue';

document.addEventListener('DOMContentLoaded', () => {
    // Find ALL validator containers on the page
    const elements = document.querySelectorAll('.rnv-container');

    elements.forEach(el => {
        // Prevent double mounting
        if (el.__vue_app__) return;

        try {
            const props = JSON.parse(el.dataset.config);
            const app = createApp(RegistrationNumberValidator, props);
            app.mount(el);
        } catch (e) {
            console.error('Registration Validator: Failed to mount component', e);
        }
    });
});