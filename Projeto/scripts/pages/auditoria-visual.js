function atualizarNC(id, valor) {
    let ncList = document.getElementById('nc-list');
    let item = document.getElementById('nc-item-' + id);

    if(valor === 'Nao') {
        if(!item){
            let div = document.createElement('div');
            div.className = 'nc-item status-Aberta';
            div.id = 'nc-item-' + id;
            div.innerHTML = `
                <strong>NC do item ${id}</strong>: Não conformidade detectada<br>
                Responsável: <input type="text" name="responsavel[${id}]" value=""><br>
                Prazo: <input type="date" name="prazo[${id}]" value=""><br>
                Status:
                <select name="status[${id}]" onchange="atualizarCor(this, ${id})">
                    <option>Aberta</option>
                    <option>Em Andamento</option>
                    <option>Resolvida</option>
                    <option>Escalonada</option>
                </select>
            `;
            ncList.appendChild(div);
        }
    } else if(item){
        ncList.removeChild(item);
    }
    atualizarAderencia();
}

function atualizarCor(sel,id){
    document.getElementById('nc-item-'+id).className='nc-item status-'+sel.value.replace(/\s/g,'');
}
