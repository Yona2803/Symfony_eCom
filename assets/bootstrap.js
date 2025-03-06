import { Application } from "@hotwired/stimulus";
import { startStimulusApp } from "@symfony/stimulus-bundle";

const app = startStimulusApp(Application);
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
