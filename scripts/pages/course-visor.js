async function getCourseSections(courseId, userId) {
    const url = new URL(`http://localhost/api/courses/${courseId}/sections/`);
    url.search = new URLSearchParams({userId: userId});

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

async function getSectionLessons(sectionId, userId) {
    const url = new URL(`http://localhost/api/sections/${sectionId}/lessons/`);
    url.search = new URLSearchParams({userId: userId});

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

async function getLessonResources(lessonId) {
    const url = `http://localhost/api/lessons/${lessonId}/resources/`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    const responseData = await response.json();

    return responseData;
}

function getResourceIconClass(contentType) {
    let iconClass = 'fa fa-file-o';

    const firstSlashIndex = contentType.indexOf('/');
    const fileType = contentType.substr(0, firstSlashIndex);

    // First look for general file types
    switch (fileType) {
        case 'image':
            iconClass = 'fa fa-file-image-o';
            break;
        case 'audio':
            iconClass = 'fa fa-file-audio-o';
            break;
        case 'video':
            iconClass = 'fa fa-file-video-o';
            break;
        case 'text':
            iconClass = 'fa fa-file-text-o';
            break;
    }

    // Then look for specific file types
    switch (contentType) {
        case 'application/pdf':
            iconClass = 'fa fa-file-pdf-o';            
            break;
        case 'application/msword':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            iconClass = 'fa fa-file-word-o';
            break;
        case 'text/javascript':
        case 'text/html':
        case 'text/css':
        case 'application/json':
            iconClass = 'fa fa-file-code-o';
            break;
        case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
        case 'application/vnd.ms-powerpoint':
            iconClass = 'fa fa-file-powerpoint-o';
            break;
        case 'application/x-7z-compressed':
        case 'application/zip':
        case 'application/vnd.rar':
            iconClass = 'fa fa-file-archive-o';
            break;
        case 'application/vnd.ms-excel':
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            iconClass = 'fa fa-file-excel-o';
            break;
    }

    return iconClass;
}

function createResourceCard(resource) {
    const card = document.createElement('li');

    const html = `
        <div class="resource-box p-2 mb-2 rounded d-inline-block">
            <i class="${getResourceIconClass(resource.contentType)} mr-2"></i>${resource.name}<a href="${resource.link}" target="_blank" class="ml-2"><i class="fa fa-download"></i></a>
        </div>
    `;

    card.innerHTML = html;

    return card;
}

function loadLessonResources(lessonId) {
    const resourcesContainer = document.getElementById('resourcesContainer');
    resourcesContainer.innerHTML = '';
    getLessonResources(lessonId).then((resources) => {
        for (const resource of resources) {
            const resourceCard = createResourceCard(resource);
            resourcesContainer.appendChild(resourceCard);
        }
    });
}

function createSectionCard(sectionInfo, lessons = [], index = 0) {
    const card = document.createElement('div');
    card.classList.add('card');
    
    const html = `
        <a href="#section-${sectionInfo.id}" class="section-collapse" data-toggle="collapse">
            <div class="card-header">
                <h5 class="d-inline">Nivel ${index + 1}: <span>${sectionInfo.title}</span></h5>
                <span class="float-right">
                    <span class="total-lessons-completed">0</span>/<span class="total-lessons">0</span>
                </span>
            </div>
        </a>
        <div id="section-${sectionInfo.id}" class="collapse ${index === 0 ? 'show' : ''}">
            <div class="card-body lessons-container">
            </div>
        </div>
    `;

    card.innerHTML = html;

    const lessonsContainer = card.getElementsByClassName('lessons-container')[0];

    let totalCompleted = 0;
    for (let i = 0; i < lessons.length; i++) {
        const lesson = lessons[i];

        if (lesson.completed) {
            totalCompleted += 1;
        }

        const lessonCard = createLessonCard(lesson, i);

        lessonsContainer.appendChild(lessonCard);
    }

    card.getElementsByClassName('total-lessons-completed')[0].innerHTML = totalCompleted;
    card.getElementsByClassName('total-lessons')[0].innerHTML = lessons.length;

    return card;
}

function createLessonCard(lessonInfo, index = 0) {
    const card = document.createElement('div');
    card.classList.add('lesson-box', 'p-3');
    
    const html = `
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" ${lessonInfo.completed ? 'checked' : ''}>
        </div>
        <span class="active-area">
            ${index + 1}. ${lessonInfo.title}
            <i class="fa fa-play-circle float-right"></i>
        </span>
    `;

    card.innerHTML = html;

    card.dataset.lessonId = lessonInfo.id;

    card.addEventListener('click', btnLoadLesson);

    return card;
}

function loadLessonInfo(lesson) {
    document.getElementById('lessonTitle').innerText = lesson.title;
    document.getElementById('lessonText').innerText = lesson.text;

    const videoPlayer = document.getElementById('videoPlayer');
    if (lesson.video) {
        videoPlayer.style.display = 'initial';
        videoPlayer.src = lesson.video;
    }
    else {
        videoPlayer.style.display = 'none';
    }

    loadLessonResources(lesson.id);
}

async function btnLoadLesson(e) {
    const btn = e.target;

    const lessonId = btn.dataset.lessonId;

    if (!lessonId) {
        return;
    }

    const lessonInfo = await getLessonInfo(lessonId);

    if (!lessonInfo) {
        return;
    }

    loadLessonInfo(lessonInfo);
}

async function getCourseInfo(courseId) {
    const url = `http://localhost/api/courses/${courseId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

async function getLessonInfo(lessonId) {
    const url = `http://localhost/api/lessons/${lessonId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
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

    document.title = `${courseInfo.title} - Didactinaut`;

    const session = await getCurrentSession();

    if (!session) {
        return;
    }

    const courseContent = document.getElementById('courseContent');
    const courseSections = await getCourseSections(courseId, session.id);

    for (let i = 0; i < courseSections.length; i++) {
        const section = courseSections[i];
        const lessons = await getSectionLessons(section.id, session.id);
        const sectionCard = createSectionCard(section, lessons, i);

        // If it is the first section
        if (i === 0) {
            const firstSectionFirstLesson = lessons[0];
            // If the first lesson of the first section exists
            if (firstSectionFirstLesson) {
                // Load this lesson
                const lessonInfo = await getLessonInfo(firstSectionFirstLesson.id);
                if (lessonInfo) {
                    loadLessonInfo(lessonInfo);
                }

            }
        }

        courseContent.append(sectionCard);
    }
});