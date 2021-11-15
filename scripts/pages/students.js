async function getStudentsReport(courseId) {
    const url = new URL('http://localhost/api/reports/');
    const params = {
        type: 'courseUsers',
        courseId: courseId
    };
    url.search = new URLSearchParams(params);

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

function createStudentCard(student) {
    const card = document.createElement('div');
    card.classList.add('col-12', 'col-lg-12', 'mb-3');
    
    const encodedStudentName = encodeURIComponent(student.fullname);

    const html = `
        <section class="card" style="width:50vw; transform: translate(10vw);">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <img  class="alumnos-img-section" src="https://avatars.dicebear.com/api/bottts/${encodedStudentName}.svg" alt="${student.fullname}">
                </div>
                <div class="col-12 col-lg-9 mb-2" >
                    <span style="font-size: 20px; font-weight: bold;">${student.fullname}</span><br><hr> 
                    <span style="font-size: 16px;">Fecha de inscripción: <span>${student.enrollDate}</span></span><br>
                    <span style="font-size: 16px;">Nivel de avance: <span>${Math.round(Number(student.completionRatio) * 100)}%</span></span><br>
                    <span style="font-size: 16px;">Precio pagado: $<span>${Number.parseFloat(student.totalPaid).toFixed(2)} MXN</span></span><br>
                    <span style="font-size: 16px;">Forma de pago: <span>${student.paymentMethod}</span></span>
                </div>
            </div>
        </section>
    `;

    card.innerHTML = html;
    
    return card;
}

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);

    const courseId = params.get('courseId');

    if (!courseId) {
        return;
    }

    const studentsContainer = document.getElementById('studentsContainer');
    const studentsReport = await getStudentsReport(courseId);

    let totalSales = 0;
    for (const student of studentsReport) {
        const studentCard = createStudentCard(student);

        studentsContainer.appendChild(studentCard);

        totalSales += Number(student.totalPaid);
    }

    document.getElementById('totalCourseMoney').innerText = totalSales.toFixed(2);
});