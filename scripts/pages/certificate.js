async function getCertificateInfo(certificateId) {
    const url = `http://localhost/api/certificates/${certificateId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

function loadCertificateInfo(certificate) {
    if (!certificate) {
        return;
    }

    document.getElementById('nombre_estudiante').innerText = certificate.userName;
    document.getElementById('nombre_curso').innerText = certificate.courseTitle;
    document.getElementById('diploma_fecha').innerText = certificate.expeditionDate;
    document.getElementById('nombre_instructor').innerText = certificate.instructorName;
    document.getElementById('codigo_certificado').innerText = certificate.id;
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    const certificateId = params.get('id');

    if (!certificateId) {
        return;
    }

    getCertificateInfo(certificateId).then(loadCertificateInfo);
});