function createReportCourseCard(course, image) {
    const card = document.createElement('div');
    card.classList.add('col-12', 'col-lg-12', 'mb-3');

    const html = `
        <section class="card" style="width: 75vw;">
            <div class="row">
                <div class="col-12 col-lg-4 col-md-5">
                    <img  class="curso-img-section" src="${image}" alt="">
                </div>
                <div class="col-12 col-lg-8 col-md-7" style="font-size: 18px;">
                    <span>${course.courseTitle}</span> 
                    <span style="margin-left: 15px;">
                        <a href="course-edition.html?id=${course.courseId}"><i class="fa fa-edit" style="color: black;"></i></a>
                    </span>
                    <span style="margin-left: 15px;">
                        <a href="#"><i class="fa fa-trash" style="color: black;"></i></a>
                    </span>
                    <hr>
                    No de alumnos inscritos: <a class="alumnos_link" href="Students.html?courseId=${course.courseId}">
                    <span>${course.totalStudents}<i class="fa fa-arrow-right"></i></span>
                    </a>
                    <br>
                    Nivel promedio: <span>${Math.round(Number(course.completionAverage) * 100)}%</span><br>
                    Total de ingresos: $ <span>${Number.parseFloat(course.totalSales ?? 0).toFixed(2)} MXN</span><br> 
                </div>
            </div>
        </section>
    `;

    card.innerHTML = html;

    return card;
}

async function getSalesReport() {
    const url = 'http://localhost/api/reports/?type=sales';

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

async function getCourseInfo(courseId) {
    const url = `http://localhost/api/courses/${courseId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

function createPaymentMethodReportCard(report) {
    const card = document.createElement('div');
    card.classList.add('col-12', 'pt-3', 'pl-3', 'text-center');
    
    const html = `
        <div>${report.paymentMethod} $ <span>${Number.parseFloat(report.totalSales ?? 0).toFixed(2)} MXN</span> </div><br>
    `;

    card.innerHTML = html;

    return card;
}

document.addEventListener('DOMContentLoaded', async () => {
    const salesReport = await getSalesReport();

    if (!salesReport) {
        return;
    }

    const courseReportContainer = document.getElementById('coursesSalesReportContainer');
    const coursesSales = salesReport.coursesSalesReport;
    for (const course of coursesSales) {
        const courseInfo = await getCourseInfo(course.courseId);

        const courseCard = createReportCourseCard(course, courseInfo.image);
        courseReportContainer.appendChild(courseCard);
    }

    const paymentMethodsReportContainer = document.getElementById('paymentMethodsReportContainer');
    const paymentMethodsReport = salesReport.paymentMethodsReport;
    
    let totalSales = 0;
    for (const paymentMethod of paymentMethodsReport) {
        const reportCard = createPaymentMethodReportCard(paymentMethod);

        paymentMethodsReportContainer.insertBefore(reportCard, paymentMethodsReportContainer.firstChild);

        totalSales += Number(paymentMethod.totalSales);
    }

    document.getElementById('salesTotal').innerText = totalSales.toFixed(2);
});