import ShoppingCart from '../ShoppingCart.js';

async function getCourseInfo(courseId) {
    const url = `http://localhost/api/courses/${courseId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

async function getCourseSections(courseId) {
    const url = `http://localhost/api/courses/${courseId}/sections/`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

async function getSectionLessons(sectionId) {
    const url = `http://localhost/api/sections/${sectionId}/lessons/`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

async function getCourseReviews(courseId, count, page = 1) {
    const url = new URL(`http://localhost/api/courses/${courseId}/reviews/`);
    
    url.searchParams.append('count', count);
    url.searchParams.append('page', page);

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

function createSectionCard(sectionInfo, lessons = [], show = false) {
    const card = document.createElement('div');
    card.classList.add('card');
    
    let lessonsHtml = '';
    for (const lesson of lessons) {
        const lessonHtml = `
            <li>
                <i class="fa fa-play-circle mr-2"></i>${lesson.title}
            </li>
        `;

        lessonsHtml += lessonHtml;
    }

    const html = `
        <a href="#section-${sectionInfo.id}" class="section-collapse" data-toggle="collapse" role="button">
            <div class="card-header">
                <h4 class="d-inline">${sectionInfo.title}</h4>
                    <span class="float-right">${lessons.length} Lecciones</span>
            </div>
        </a>
        <div id="section-${sectionInfo.id}" class="collapse ${show ? 'show' : ''}">
            <div class="card-body">
                <ul class="list-unstyled">
                    ${lessonsHtml}
                </ul>
            </div>
        </div>
    `;

    card.innerHTML = html;

    return card;
}

function createReviewCard(reviewInfo) {
    const card = document.createElement('div');
    card.classList.add('card', 'mb-2');
    
    const html = `
        <div class="card-body">
            <div class="didactinaut-profile-picture float-left pr-2">
                <img src="https://avatars.dicebear.com/api/bottts/${reviewInfo.user.name}.svg?background=%23bcbcbc" alt="${reviewInfo.user.name}">
            </div>
            <h5 class="card-title">${reviewInfo.user.name}</h5>
            <h6 class="card-subtitle text-muted mb-2">${reviewInfo.date}</h6>
            <div class="stars">
            </div>
            <p>
                ${reviewInfo.body}
            </p>
        </div>
    `;

    card.innerHTML = html;

    setStars(card.getElementsByClassName('stars')[0], reviewInfo.score);

    return card;
}

function setStars(element, score) {
    let totalScore = Number(score);
    totalScore = Math.round(totalScore);

    const emptyStars = 5 - totalScore;

    for (let i = 0; i < totalScore; i++) {
        element.innerHTML += '<i class="fa fa-star"></i>';
    }

    for (let i = 0; i < emptyStars; i++) {
        element.innerHTML += '<i class="fa fa-star-o"></i>';
    }
}

function loadCourseInfo(courseInfo) {
    document.title = `${courseInfo.title} - Didactinaut`
    document.getElementById('courseImage').src = courseInfo.image;
    document.getElementById('courseTitle').textContent = courseInfo.title;
    document.getElementById('courseInstructor').textContent = `${courseInfo.instructor.name} ${courseInfo.instructor.lastname}`;
    document.getElementById('courseCreationDate').textContent = courseInfo.publicationDate;
    document.getElementById('courseLastUpdateDate').textContent = courseInfo.lastUpdate;
    document.getElementById('coursePrice').textContent = courseInfo.price;
    document.getElementById('courseTotalLessons').textContent = courseInfo.totalLessons;
    document.getElementById('courseTotalStudents').textContent = courseInfo.totalStudents;
    document.getElementById('courseLongDescription').textContent = courseInfo.description;
    document.getElementById('courseScore').textContent = courseInfo.score;
    if (courseInfo.score) {
        setStars(document.getElementById('courseStars'), courseInfo.score);
    }

    // Prepare the shopping buttons
    const btnAddToCart = document.getElementById('btnAddToCart');
    const btnBuyNow = document.getElementById('btnBuyNow');

    btnAddToCart.dataset.productId = courseInfo.productId;
    btnBuyNow.dataset.productId = courseInfo.productId;

    btnAddToCart.addEventListener('click', (e) => {
        const btn = e.target;
        const productId = btn.dataset.productId;

        ShoppingCart.addProduct(productId);
        // alert('Producto agregado con éxito');
        Swal.fire({
            icon: 'success',
            title: '<h2 style="color: white;">Producto agregado con éxito</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
    });

    btnBuyNow.addEventListener('click', (e) => {
        const btn = e.target;
        const productId = btn.dataset.productId;

        ShoppingCart.addProduct(productId);
        window.location.href = '/FrontEnd/cart.html';
    });
}

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    const responseData = await response.json();

    return responseData;
}

async function sendMessage(message, chatId) {
    const url = `http://localhost/api/chats/${chatId}/messages/`;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(message)
    });

    return response.ok;
}

async function createChat(chat, userId) {
    const url = `http://localhost/api/users/${userId}/chats/`;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(chat)
    });

    if (response.ok) {
        const responseData = await response.json();
        return responseData.id;
    }

    return null;
}

async function sendMessageBtn(e) {
    const button = e.target;
    const instructorId = button.dataset.instructorId;

    if (!instructorId) {
        return;
    }

    const chatSubject = document.getElementById('subject-text').value.trim();
    const messageBody = document.getElementById('message-text').value.trim();

    if (chatSubject == '') {
        // alert('Escriba un asunto para el mensaje')
        Swal.fire({
            icon: 'warning',
            title: '<h2 style="color: white;">Escriba un asunto para el mensaje</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    const session = await getCurrentSession();

    if (!session) {
        // alert('Necesita iniciar sesión para enviar mensajes')
        Swal.fire({
            icon: 'warning',
            title: '<h2 style="color: white;">Necesita iniciar sesión para enviar mensajes</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    const chat = {
        subject: chatSubject,
        receptorId: instructorId
    }

    const chatId = await createChat(chat, session.id);

    if (!chatId) {
        // alert('Lo sentimos, no se pudo enviar el mensaje.');
        Swal.fire({
            icon: 'error',
            title: '<h2 style="color: white;">Lo sentimos, no se pudo enviar el mensaje.</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    if (messageBody !== '') {
        const initialMessage = {
            senderId: session.id,
            body: messageBody
        }
    
        const success = await sendMessage(initialMessage, chatId);
    
        if (!success) {
            // alert('Lo sentimos, no se pudo enviar el mensaje.');
            Swal.fire({
                icon: 'error',
                title: '<h2 style="color: white;">Lo sentimos, no se pudo enviar el mensaje.</h2>',
                confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
                confirmButtonColor: '#48e5c2',
                showConfirmButton: false,
                timer: 1200,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseenter', Swal.resumeTimer)
                },
                background: '#333333',
            })  
            return;
        }
    }

    window.location.href = `/FrontEnd/Messages.html?chatId=${chatId}`;
}

async function addReview(review, courseId) {
    const url = `http://localhost/api/courses/${courseId}/reviews/`;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(review)
    });
    
    return response.ok;
}

async function reviewFormSubmit(e) {
    e.preventDefault();
    const form = e.target;

    const courseId = form.dataset.courseId;

    if (!courseId) {
        return;
    }

    const session = await getCurrentSession();

    if (!session) {
        // alert('Necesita iniciar sesión para publicar un review');
        Swal.fire({
            icon: 'warning',
            title: '<h2 style="color: white;">Inicia sesión para publicar un review</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    const reviewBodyInput = document.getElementById('reviewBody');
    const reviewScoreInput = document.getElementById('reviewScore');

    const reviewBody = reviewBodyInput.value.trim();
    const reviewScore = reviewScoreInput.value;

    if (reviewBody === '') {
        // alert('Escriba un mensaje en su reseña');
        Swal.fire({
            icon: 'warning',
            title: '<h2 style="color: white;">Escribe un mensaje en la reseña</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    const review = {
        body: reviewBody,
        score: reviewScore,
        userId: session.id
    };

    const success = await addReview(review, courseId);

    if (!success) {
        // alert('No se pudo publicar la reseña');
        Swal.fire({
            icon: 'error',
            title: '<h2 style="color: white;">No se pudo publicar la reseña</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
        return;
    }

    // alert('Reseña publicada con éxito');
    Swal.fire({
            icon: 'success',
            title: '<h2 style="color: white;">Reseña publicada con éxito</h2>',
            confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
            confirmButtonColor: '#48e5c2',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseenter', Swal.resumeTimer)
            },
            background: '#333333',
        })
    // Reset
    form.reset();
}

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);

    const courseId = params.get('id');

    if (!courseId) {
        return;
    }

    const courseInfo = await getCourseInfo(courseId);

    if (!courseInfo) {
        return;
    }

    loadCourseInfo(courseInfo);

    // Set the send message button
    const btnSendMessage = document.getElementById('btnSendMessage');
    btnSendMessage.dataset.instructorId = courseInfo.instructor.id;
    btnSendMessage.addEventListener('click', sendMessageBtn);

    // Set the review form
    const reviewForm = document.getElementById('reviewForm');
    reviewForm.dataset.courseId = courseInfo.id;
    reviewForm.addEventListener('submit', reviewFormSubmit);

    // Load course content
    const courseContent = document.getElementById('courseContent');
    const courseSections = await getCourseSections(courseId);

    let first = true;
    for (const section of courseSections) {
        const lessons = await getSectionLessons(section.id);
        const sectionCard = createSectionCard(section, lessons, first);

        courseContent.append(sectionCard);

        first = false;
    }

    // Load course reviews
    const reviewsContainer = document.getElementById('courseReviews');
    const courseReviews = await getCourseReviews(courseId, 10);

    document.getElementById('courseTotalReviews').textContent = courseReviews.length;

    if (courseReviews.length > 0) {
        reviewsContainer.innerHTML = '';
    }

    for (const review of courseReviews) {
        const reviewCard = createReviewCard(review);
        reviewsContainer.append(reviewCard);
    }
});