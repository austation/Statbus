import './styles/app.scss';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import Alpine from 'alpinejs'

window.bootstrap = bootstrap;
window.Alpine = Alpine

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

Alpine.start()
 

