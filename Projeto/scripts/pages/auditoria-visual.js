document.addEventListener("DOMContentLoaded", function() {
    const progressContainer = document.getElementById("progress");
    const percent = parseInt(progressContainer.dataset.percent) || 0;
    const circle = progressContainer.querySelector("circle.progress");
    const text = progressContainer.querySelector(".progress-text");

    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;

    circle.style.strokeDasharray = circumference;
    circle.style.strokeDashoffset = circumference;

    // Determina a cor do círculo
    if(percent >= 75){
        circle.style.stroke = "#28a745"; // verde
        text.style.color = "#155724";
    } else if(percent >= 50){
        circle.style.stroke = "#ffc107"; // amarelo
        text.style.color = "#856404";
    } else {
        circle.style.stroke = "#dc3545"; // vermelho
        text.style.color = "#721c24";
    }

    let current = 0;
    const interval = setInterval(() => {
        if(current >= percent){
            clearInterval(interval);
        } else {
            current++;
            text.textContent = current + "%";
            const offset = circumference - (current / 100) * circumference;
            circle.style.strokeDashoffset = offset;
        }
    }, 15);
});
