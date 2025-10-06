import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();

import LiveController from '@symfony/ux-live-component';
app.register('live', LiveController);

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
