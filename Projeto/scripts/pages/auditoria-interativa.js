document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll(".audit-row");
    const ncList = document.getElementById("nc-list");

    rows.forEach(row => {
        const radios = row.querySelectorAll('input[type="radio"]');

        radios.forEach(radio => {
            radio.addEventListener("change", () => {
                row.classList.remove("selected");

                if (radio.value === "Nao") {
                    row.classList.add("selected");
                    addNcField(row);
                } else {
                    removeNcField(row);
                }
            });
        });
    });

    function addNcField(row) {
        const itemId = row.querySelector('input[type="radio"]').name.match(/\d+/)[0];
        if (!document.getElementById(`nc-${itemId}`)) {
            const ncDiv = document.createElement("div");
            ncDiv.className = "nc-item";
            ncDiv.id = `nc-${itemId}`;
            ncDiv.innerHTML = `<label>Observação/NC para "${row.cells[1].textContent}":</label>
                               <input type="text" name="nc[${itemId}]" placeholder="Descreva a não conformidade">`;
            ncList.appendChild(ncDiv);
        }
    }

    function removeNcField(row) {
        const itemId = row.querySelector('input[type="radio"]').name.match(/\d+/)[0];
        const ncDiv = document.getElementById(`nc-${itemId}`);
        if (ncDiv) ncDiv.remove();
    }
});
