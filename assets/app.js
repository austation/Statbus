import './styles/app.scss';
import * as bootstrap from 'bootstrap'
import Alpine from 'alpinejs'
import dayjs from 'dayjs';

var relativeTime = require('dayjs/plugin/relativeTime')
var utc = require('dayjs/plugin/utc')

const autoComplete = require("@tarekraafat/autocomplete.js");

dayjs.extend(utc)
dayjs.extend(relativeTime)

window.bootstrap = bootstrap;
window.Alpine = Alpine

const expiredNote = document.getElementById('expiredNoteModal')
if(expiredNote !== null) {
const expiredNoteModalElement = new bootstrap.Modal(expiredNote)
expiredNoteModalElement.show()
}

const searchForm = document.getElementById('globalSearchForm')
const globalSearchEl = document.getElementById('globalSearch')
const ignoreInputFocusEls = ['input','textarea']
document.addEventListener("keydown", (e) => {
	if('Slash' == e.code){
		if(!ignoreInputFocusEls.includes(document.activeElement.tagName.toLowerCase())){
			e.preventDefault()
            searchForm.classList.toggle('w-100')
		}
		globalSearchEl.focus()
	}
});

globalSearchEl.addEventListener("blur", (e) => {
    searchForm.classList.toggle('w-100')
})

const searchUrl = searchForm.getAttribute('action')
const searchDest = searchForm.dataset.searchdest

const autoCompleteConfig = {
    selector: "#globalSearch", 
    data: {
        src: async () => {
            const source = await fetch(searchUrl, {
                method: "post",
                headers: {
                  Accept: "application/json",
                  "Content-Type": "application/json",
                },
                body: JSON.stringify({
                  term: document.querySelector("#globalSearch").value,
                }),
              });
              const data = await source.json();
              return data.ckeys;
        },
        key: 'ckeys'
    },
    resultItem: {
        tag: "li",
        class: "list-group-item",
        highlight: "autoComplete_highlight",
        selected: "autoComplete_selected active"
    },
    debounce: 300,
    threshold: 3,
    highlight: true,
    resultsList: {
        tabSelect: true,
        tag: "ul",
        id: "globalSearchResults",
        class: "list-group",
        maxResults: 5,
        noResults: true,
    },
    events: {
        input: {
            focus() {
                document.getElementById('globalSearchForm')
                const inputValue = autoCompleteJS.input.value;
                if (inputValue.length) autoCompleteJS.start();
            },
        },
    },
}

const autoCompleteJS = new autoComplete(autoCompleteConfig);
document.getElementById('globalSearch').addEventListener("selection", function (event) {
    window.location = `${searchDest}/${event.detail.selection.value}`
});
Alpine.start()

