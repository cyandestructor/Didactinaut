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
        alert('Producto agregado con éxito');
    });

    btnBuyNow.addEventListener('click', (e) => {
        const btn = e.target;
        const productId = btn.dataset.productId;

        ShoppingCart.addProduct(productId);
        window.location.href = '/FrontEnd/cart.html';
    });
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