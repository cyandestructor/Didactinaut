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

async function createCourse(course) {
    const data = {
        title: course.title,
        description: course.description,
        price: course.price,
        instructorId: course.instructorId
    };
    
    const baseUrl = 'http://localhost/api/';
    const url = baseUrl + 'courses/';

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const responseData = await response.json();

    const courseId = responseData.id;

    // If theres is an image, upload it
    if (course['image']) {
        // 'api/courses/$id
        const changeImgUrl = `${url}${courseId}/image/`

        await fetch(changeImgUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': course.image.type
            },
            body: course.image
        });
    }

    return courseId;
}

async function  uploadCourseInfo(info, userId) {
    const courseInfo = {
        title: info.title,
        description: info.description,
        price: info.price,
        instructorId: userId,
        image: info.image
    }

    // Create course in DB
    const courseId = await createCourse(courseInfo);

    // Create each section
    for (const section of info.sections) {
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
                text: '',
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

    await publishCourse(courseId);
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
        lesson.video = lessonForm.getElementsByClassName('video-input')[0].files[0];

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

function getCourseInfo() {
    const info = {};

    info.title = document.getElementById('courseTitle_input').value;
    info.description = document.getElementById('courseDescription_input').value;
    info.price = document.getElementById('courseCost_input').value;
    info.image = document.getElementById('courseImage_input').files[0];

    info.sections = getLevelsInfo();

    return info;
}

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    const responseData = await response.json();

    return responseData;
}

async function publishCourse(courseId) {
    const baseUrl = 'http://localhost/api/';
    const url = baseUrl + 'courses/' + courseId;

    const data = {
        published: true
    };

    await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
}

document.getElementById('form_coursecreation').addEventListener('submit', async (e) => {
    e.preventDefault();

    const courseInfo = getCourseInfo();
    // console.log(courseInfo);

    let session = null;
    try {
        session = await getCurrentSession();
    } catch (error) {
        console.log(error);
    }

    if (session) {
        try {
            await uploadCourseInfo(courseInfo, session.id);
            console.log('Course created');
        } catch (error) {
            console.log(error);
        }
    }
    else {
        console.log('No current session');
    }
});