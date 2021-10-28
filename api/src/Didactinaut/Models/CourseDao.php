<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Course;

class CourseDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function createCourse(Course $course)
    {
        $courseID = -1;

        $sql = 'CALL CreateCourse(?, ?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $course->title,
            $course->description,
            $course->product['price'],
            $course->instructor['id']
        ]);

        $statement->bindColumn(1, $courseID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $courseID;
    }

    public function getCourse($courseID)
    {
        $course = new Course();
        
        $sql = 'CALL GetCourseInfo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $courseID);
        $statement->execute();

        if($row = $statement->fetch()){
            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->publicationDate = $row['publication_date'];
            $course->lastUpdate = $row['last_update'];
            $course->extra['totalStudents'] = $row['total_students'];
            $course->extra['totalLessons'] = $row['total_lessons'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];
        }
        else{
            return null;
        }

        return $course;
    }

    public function getCourses($limit, $offset = 0, $orderBy = '')
    {
        switch (strtolower($orderBy)) {
            case 'score':
                return $this->getCoursesByScore($limit, $offset);
            case 'sales':
                return $this->getCoursesBySales($limit, $offset);
            case 'publication':
                return $this->getCoursesByPublication($limit, $offset);
            default:
                return $this->getCoursesList($limit, $offset);
        }
    }

    public function getCoursesByScore($limit, $offset = 0)
    {            
        $courses = [];

        $sql = 'CALL GetTopScoreCourses(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function getCoursesBySales($limit, $offset = 0)
    {            
        $courses = [];

        $sql = 'CALL GetTopSalesCourses(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function getCoursesByPublication($limit, $offset = 0)
    {            
        $courses = [];

        $sql = 'CALL GetMostRecentCourses(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function getCoursesList($limit, $offset = 0)
    {            
        $courses = [];

        $sql = 'CALL GetCourses(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function editCourse(Course $course)
    {
        $sql = 'CALL EditCourse(?, ?, ?, ?, b?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $course->id,
            $course->title,
            $course->description,
            $course->product['price'],
            $course->published
        ]);
    }

    public function setCourseImage($courseId, $image, $contentType)
    {
        $sql = 'CALL SetCourseImage(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $courseId,
            $image,
            $contentType
        ]);
    }

    public function addCategory($courseID, $categoryID)
    {
        $sql = 'CALL AddCourseCategory(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $courseID,
            $categoryID
        ]);
    }

    public function deleteCategory($courseID, $categoryID)
    {
        $sql = 'CALL DeleteCourseCategory(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $courseID,
            $categoryID
        ]);
    }

    public function getUserCourses($userID, $limit, $offset = 0)
    {
        $courses = [];

        $sql = 'CALL GetUserCourses(?, ?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $userID,
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->extra['totalLessons'] = $row['course_total_lessons'];
            $course->extra['enrollDate'] = $row['enroll_date'];
            $course->extra['lastTimeChecked'] = $row['last_time_checked'];
            $course->extra['certificateId'] = $row['certificate_id'];
            $course->extra['totalCompletedLessons'] = $row['total_completed_lessons'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function getResultCourses($query, $limit, $offset = 0, $filters = [])
    {
        $courses = [];

        $sql = 'CALL SearchCourses(?, ?, ?, ?, ?, ?)';

        $fromDate = $filters['from'] ?? null;
        $toDate = $filters['to'] ?? null;
        $categoryId = $filters['category'] ?? null;

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $query,
            $limit,
            $offset,
            $fromDate,
            $toDate,
            $categoryId
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }

    public function getInstructorCourses($userID, $limit, $offset = 0, $onlyPublished = true)
    {
        $courses = [];

        $sql = 'CALL GetInstructorCourses(?, b?, ?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $userID,
            $onlyPublished,
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $course = new Course();

            $course->id = $row['course_id'];
            $course->imageId = $row['course_image'];
            $course->title = $row['course_title'];
            $course->description = $row['course_description'];
            $course->product['id'] = $row['product_id'];
            $course->product['price'] = $row['course_price'];
            $course->instructor['id'] = $row['instructor_id'];
            $course->instructor['name'] = $row['instructor_name'];
            $course->instructor['lastname'] = $row['instructor_lastname'];
            $course->extra['score'] = $row['course_score'];
            $course->published = $row['course_published'];

            $courses[] = $course;
        }

        return $courses;
    }
}