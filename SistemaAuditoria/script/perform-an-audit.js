function carregarItens() {
    let checklist_id = document.getElementById("checklist_id").value;
    if (checklist_id) {
        window.location.href = "realizar_auditoria.php?checklist_id=" + checklist_id;
    }
}