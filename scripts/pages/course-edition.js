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

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    const responseData = await response.json();

    return responseData;
}

function createLessonCard(lesson) {
    const card = document.createElement('div');
    card.classList.add('container_lessonvideo');

    const html = `
        <form class='lesson-edit-form' data-lesson-id='${lesson.id}'>
            <i class='fa fa-play-circle mr-2'></i>
            Nombre de lección:
            <input name='title' class='mb-3 lesson-title-input' type='text' value='${lesson.title}'>
            <br>
            <span class='ml-4'>
                Texto:
            </span>
            <textarea name='text' class='lesson-text-input'>${lesson.text ?? ''}</textarea>
            <div class="mt-2">
                <button class="btn_nuevoNivel" type="submit">Guardar lección</button>
            </div>
        </form>
    `;

    card.innerHTML = html;

    const lessonEditForm = card.getElementsByClassName('lesson-edit-form')[0];
    lessonEditForm.addEventListener('submit', editLessonFormSubmit);

    return card;
}

async function editLessonFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const lessonId = form.dataset.lessonId;

    if (!lessonId) {
        return;
    }

    const title = form.elements['title'].value.trim();
    const text = form.elements['text'].value.trim();

    const data = {
        title: title,
        text: text
    };

    const url = `http://localhost/api/lessons/${lessonId}`;

    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (response.ok) {
        alert('Lección editada');
    }
}

async function editSectionFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const sectionId = form.dataset.sectionId;

    if (!sectionId) {
        return;
    }

    const title = form.elements['title'].value.trim();
    const price = form.elements['price'].value.trim();

    const data = {
        title: title
    };

    if (price !== '') {
        data['price'] = price;
    }

    const url = `http://localhost/api/sections/${sectionId}`;

    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (response.ok) {
        alert('Sección editada');
    }
}

async function setCourseImage(courseId, image) {
    const url = `http://localhost/api/courses/${courseId}/image`;

    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': image.type
        },
        body: image
    });

    return response.ok;
}

async function btnEditCourse(e) {
    const button = e.target;
    const courseId = button.dataset.courseId;

    if (!courseId) {
        return;
    }

    const courseTitleInput = document.getElementById('courseTitle_input');
    const courseDescriptionInput = document.getElementById('courseDescription_input_edit');
    const courseImageInput = document.getElementById('courseImage_input_edit');
    const coursePriceInput = document.getElementById('courseCost_input_edit');

    const title = courseTitleInput.value.trim();
    const description = courseDescriptionInput.value.trim();
    const price = coursePriceInput.value.trim();

    if (courseImageInput.files.length > 0) {
        const imageUpdated = await setCourseImage(courseId, courseImageInput.files[0]);

        if (!imageUpdated) {
            alert('No se pudo editar la imagen del curso');
        }
        else {
            alert('Se he editado la imagen del curso');
        }
    }

    const data = {
        title: title,
        description: description,
        price: price
    };

    const success = await editCourseInfo(courseId, data);

    if (!success) {
        alert('No se pudo editar la información del curso');
    }
    else {
        alert('Se he editado la información del curso');
    }
}

function createSectionCard(section) {
    const card = document.createElement('div');
    card.classList.add('accordion');
    
    const html = `
        <div class='card'>
            <div class='card-header'>
                <form class='edit-section-form' data-section-id='${section.id}'>
                    <label class='d-inline'>
                        Nombre de nivel:
                        <input name='title' class='level-name-input' type='text' value='${section.title}'>
                        <input name='price' class='level-price-input' type='text' placeholder='Precio' value='${section.price ?? ''}'>
                        <button class="btn_nuevoNivel" type="submit">Guardar Nivel</button>
                    </label>
                </form>
            </div>
            <ul class='section_lesson_container list-unstyled'>
                
            </ul>
        </div>
    `;

    card.innerHTML = html;

    const sectionEditForm = card.getElementsByClassName('edit-section-form')[0];
    sectionEditForm.addEventListener('submit', editSectionFormSubmit);

    return card;
}

function loadCourseInfo(courseInfo) {
    const courseTitleInput = document.getElementById('courseTitle_input');
    const coursePriceInput = document.getElementById('courseCost_input_edit');
    const courseImage = document.getElementById('courseImage');
    const courseDescriptionInput = document.getElementById('courseDescription_input_edit');
    const btnEditCourse = document.getElementById('btnUpdateCourse');
    const btnEditCourseInfo = document.getElementById('btnEditCourseInfo');

    courseTitleInput.value = courseInfo.title;
    coursePriceInput.value = courseInfo.price;
    courseImage.src = courseInfo.image;
    courseDescriptionInput.value = courseInfo.description;
    btnEditCourse.dataset.courseId = courseInfo.id;
    btnEditCourseInfo.dataset.courseId = courseInfo.id;
}

async function getLessonInfo(lessonId) {
    const url = `http://localhost/api/lessons/${lessonId}`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

async function addLessonResource(resourceData) {
    const lessonId = resourceData.lesson;

    const baseUrl = 'http://localhost/api/';
    const url = `${baseUrl}lessons/${lessonId}/resources/`;

    await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': resourceData.resource.content.type,
            'X-Resource-Name': resourceData.resource.name
        },
        body: resourceData.resource.content
    });
}

async function addLessonVideo(videoData) {
    const lessonId = videoData.lesson;

    const baseUrl = 'http://localhost/api/';
    const url = `${baseUrl}lessons/${lessonId}/video/`;

    await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': videoData.video.type
        },
        body: videoData.video
    });
}

async function createLesson(lesson) {
    const sectionId = lesson.section;

    const baseUrl = 'http://localhost/api/';
    const url = `${baseUrl}sections/${sectionId}/lessons/`;

    const data = {
        title: lesson.title,
        text: lesson.text
    };

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const responseData = await response.json();

    return responseData.id;
}

async function createSection(section) {
    const courseId = section.course;

    const baseUrl = 'http://localhost/api/';
    const url = `${baseUrl}courses/${courseId}/sections/`;

    const data = {
        title: section.title,
        price: section.price
    };

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const responseData = await response.json();

    return responseData.id;
}

function getFilesInfo(lesson) {
    const info = [];
    
    const fileForms = lesson.getElementsByClassName('file-form');
    for (const fileForm of fileForms) {
        const file = {};
        
        file.name = fileForm.getElementsByClassName('file-name-input')[0].value;
        file.content = fileForm.getElementsByClassName('file-input')[0].files[0];
        
        info.push(file);
    }

    return info;
}

function getLessonsInfo(level) {
    const info = [];

    const lessonForms = level.getElementsByClassName('lesson-form');
    for (const lessonForm of lessonForms) {
        const lesson = {};

        lesson.title = lessonForm.getElementsByClassName('lesson-title-input')[0].value;
        lesson.text = lessonForm.getElementsByClassName('lesson-text-input')[0].value;
        const videoInput = lessonForm.getElementsByClassName('video-input')[0];

        lesson.video = videoInput.files.length > 0 ? videoInput.files[0] : null;

        lesson.files = getFilesInfo(lessonForm);

        info.push(lesson);
    }

    return info;
}

function getLevelsInfo() {
    const info = [];
    
    const levelForms = document.getElementsByClassName('level-form');
    for (const levelForm of levelForms) {
        const level = {};

        level.title = levelForm.getElementsByClassName('level-name-input')[0].value;
        level.price = levelForm.getElementsByClassName('level-price-input')[0].value;

        level.lessons = getLessonsInfo(levelForm);

        info.push(level);
    }

    return info;
}

async function editCourseInfo(courseId, info) {
    const url = `http://localhost/api/courses/${courseId}`;

    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(info)
    });

    return response.ok;
}

async function addCourseNewContent(courseId) {
    const sections = getLevelsInfo();

    for (const section of sections) {
        const sectionInfo = {
            title: section.title,
            price: section.price != '' ? section.price : 0,
            course: courseId
        }

        const sectionId = await createSection(sectionInfo);

        // Create each lesson
        for (const lesson of section.lessons) {
            const lessonInfo = {
                title: lesson.title,
                text: lesson.text,
                section: sectionId
            };

            const lessonId = await createLesson(lessonInfo);

            // Upload video if available
            if (lesson.video) {
                await addLessonVideo({
                    lesson: lessonId,
                    video: lesson.video
                });
            }

            // Upload resources if any
            for (const file of lesson.files) {
                await addLessonResource({
                    lesson: lessonId,
                    resource: file
                });
            }
        }
    }
}

async function btnAddNewContent(e) {
    const button = e.target;
    button.disabled = true;
    const courseId = button.dataset.courseId;

    try {
        await addCourseNewContent(courseId);
        alert('Se ha agregado el nuevo contenido al curso');
        button.disabled = false;
    } catch (error) {
        console.log(error);
    }
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

    const session = await getCurrentSession();

    if (!session) {
        return;
    }

    // If it is not a course created by the current logged user
    if (courseInfo.instructor.id != session.id) {
        return;
    }

    loadCourseInfo(courseInfo);

    // Set add new content button
    document.getElementById('btnUpdateCourse').addEventListener('click', btnAddNewContent);
    document.getElementById('btnEditCourseInfo').addEventListener('click', btnEditCourse);

    // Load course content
    const courseContent = document.getElementById('editedCourseContent');
    const courseSections = await getCourseSections(courseId);

    for (const section of courseSections) {
        const sectionCard = createSectionCard(section);

        const lessons = await getSectionLessons(section.id);
        
        const lessonContainer = sectionCard.getElementsByClassName('section_lesson_container')[0];

        for (const lesson of lessons) {
            const lessonInfo = await getLessonInfo(lesson.id);
            if (lessonInfo) {
                const lessonCard = createLessonCard(lessonInfo);
                lessonContainer.appendChild(lessonCard);
            }
        }

        courseContent.append(sectionCard);
    }
});