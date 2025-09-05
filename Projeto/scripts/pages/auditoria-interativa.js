const ncList = document.getElementById('nc-list');
document.querySelectorAll('.checklist-item input[type=radio]').forEach(r=>{
    r.addEventListener('change', ()=>{
        ncList.innerHTML = '';
        document.querySelectorAll('.checklist-item').forEach(tr=>{
            const radios = tr.querySelectorAll('input[type=radio]');
            radios.forEach(rad=>{
                if(rad.checked && rad.value=='Nao'){
                    const nc = document.createElement('div');
                    nc.className='nc-item';
                    nc.innerText = 'Não conformidade no item: '+tr.cells[0].innerText;
                    ncList.appendChild(nc);
                }
            })
        });
    })
})
