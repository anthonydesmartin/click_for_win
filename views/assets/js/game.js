const clicker = document.getElementById("clicker");
const counter = document.getElementById("counter");
const timeshow = document.getElementById("timeshow");

let begin = 0;
let count = 0;
let time = 2.0;
timeshow.innerHTML = time;
let on = true;
let timer = null;

clicker.onclick = () => {
  if (begin === 0) {
    count++;
    counter.innerHTML = count.toString();
    startTimer();
    begin++;
  } else {
    count++;
    counter.innerHTML = count.toString();
  }
};

const startTimer = () => {
  timer = setInterval(() => {
    if (time.toFixed(2) == 0.0) {
      clicker.remove();
      timeshow.remove();
      counter.innerHTML = (count / 10).toFixed(1).toString();
      send();
      stopTimer();
    } else {
      time = time - 0.01;
      timeshow.innerHTML = time.toFixed(2);
    }
  }, 10);
};

const stopTimer = () => {
  clearInterval(timer);
};

function send() {
  $.ajax(
      {
        type: 'POST',
        url: location.pathname+'?ajax=true',
        data: {
          'count': count
        },
        success: () => {

        }
      }
  )
}
