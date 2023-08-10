import './styles/app.scss';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import Alpine from 'alpinejs'
import dayjs from 'dayjs';

var relativeTime = require('dayjs/plugin/relativeTime')
var utc = require('dayjs/plugin/utc')

dayjs.extend(utc)
dayjs.extend(relativeTime)

window.bootstrap = bootstrap;
window.Alpine = Alpine

// const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
// const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))


const timeElements = document.querySelectorAll('time')
const timeElementsList = [...timeElements].map(function(e){
    var date = dayjs(e.innerText).utc()
    e.innerText = date.fromNow()
    e.setAttribute('data-bs-title', date.format('YYYY-MM-DD'))
    e.setAttribute('data-bs-toggle',"tooltip")
    new bootstrap.Tooltip(e)
})
Alpine.start()

