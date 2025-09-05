function atualizarAderencia() {
    let radios = document.querySelectorAll('input[type=radio]:checked');
    let total = 0, sim = 0;
    radios.forEach(r => {
        if(r.value != 'NA'){ total++; if(r.value=='Sim') sim++; }
    });
    let perc = total>0 ? ((sim/total)*100).toFixed(2) : 0;
    let elem = document.getElementById('aderencia');
    if(elem){
        elem.innerText = perc + '%';
        elem.style.color = perc>=80?'green':perc>=50?'orange':'red';
    }
}
