const typeTogglerEl = document.getElementById("typeToggler");

const createLogToggler = (type) => {
    let name = document.querySelector(`.${type}`).dataset.name
    var inputEl = document.createElement("input");
    inputEl.setAttribute("type", "checkbox");
    inputEl.setAttribute("id", `checkbox_${type}`);
    inputEl.setAttribute("checked", true);
    inputEl.value = type;
    inputEl.classList.add("btn-check");
  
    var label = document.createElement("label");
    label.classList.add("btn", "btn-outline-primary");
    label.setAttribute("for", `checkbox_${type}`);
    label.innerText = `${name}`;
    inputEl.addEventListener("change", (e) => {
      const target = e.currentTarget.value;
      const elements = document.querySelectorAll(`.${target}`)
      elements.forEach((e) => {
          e.classList.toggle('visually-hidden')
      })
    });
  
    typeTogglerEl.appendChild(inputEl);
    typeTogglerEl.appendChild(label);
  };

let types = []
const logElements = document.querySelectorAll(".timeline-entry")
logElements.forEach((e) => {
    type = e.classList[e.classList.length - 1]
    types.push(type)
})
types = [...new Set(types)]
types.map((t) => {
    createLogToggler(t);
  });