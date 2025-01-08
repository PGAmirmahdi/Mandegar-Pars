const body = document.getElementById("body")

function load() {
    document.getElementById('No1').classList.add('load')
    document.getElementById('No2').classList.add('load')
    document.getElementById('No3').classList.add('load')
    body.classList.add('load')
    body.getElementsByClassName('khat')[0].classList.add('load')
    body.getElementsByClassName('back2')[0].classList.add('load')
}
if(navigator.onLine){
    setTimeout(() => {  location.href="http://mpsystem.ir/"; }, 3000);
}
