const checklist = document.querySelectorAll('.checklist-table tbody tr');
const progress = document.getElementById('progress');
function atualizarAderencia(){
    let total=0, sim=0;
    checklist.forEach(tr=>{
        const resposta = tr.cells[1].innerText;
        if(resposta=='Sim' || resposta=='Nao'){ total++; }
        if(resposta=='Sim'){ sim++; }
    });
    const perc = total>0?Math.round(sim/total*100):0;
    progress.innerText=perc+'%';
    progress.style.setProperty('--percent', perc+'%');
    if(perc>=80) progress.className='progress-circle green';
    else if(perc>=50) progress.className='progress-circle yellow';
    else progress.className='progress-circle red';
}
window.addEventListener('load', atualizarAderencia);
