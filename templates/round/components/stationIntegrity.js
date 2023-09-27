async function fetchExternalData() {
  const targetEl = document.getElementById("integrity");
  const response = await fetch(`${window.location.href}/sb_who?json`);
  if (500 == response.status) {
    targetEl.innerHTML = `Unable to determine station integrity`;
  }
  var data = await response.json();
  data = data.stat.data;
  if (!data) {
    targetEl.innerHTML = `Unable to determine station integrity`;
  }
  const survivors =
    Object.keys(data.escapees.humans).length +
    Object.keys(data.escapees.silicons).length +
    Object.keys(data.escapees.others).length;
  const abandoned =
    Object.keys(data.abandoned.humans).length +
    Object.keys(data.abandoned.silicons).length +
    Object.keys(data.abandoned.others).length;
  targetEl.innerHTML = `<a href="${window.location.href}/sb_who" class="icon-link btn btn-primary"><i class="fa-solid fa-users-viewfinder"></i> View Full Roster</a>`;

  let progressBar = document.getElementById("station_integrity_bar");
  progressBar.style.width = `${data["additional data"]["station integrity"]}%`;
  progressBar.innerText = `${data["additional data"]["station integrity"]}%`;
  progressBar.parentElement.setAttribute(
    "aria-valuenow",
    data["additional data"]["station integrity"]
  );

let total = survivors + abandoned

let survival_percent = (survivors/total) * 100
let abandoned_percent = (abandoned/total) * 100

let survivalBar = document.getElementById('survivors_bar')
survivalBar.style.width = `${survival_percent}%`
let survivalChild = survivalBar.querySelector('.progress-bar')
survivalChild.innerText = `${survivors} crew escaped`

let leftBar = document.getElementById('left_behind_bar')
leftBar.style.width = `${abandoned_percent}%`
let leftChild = leftBar.querySelector('.progress-bar')
leftChild.innerText = `${abandoned} crew abandoned`
}
fetchExternalData();
