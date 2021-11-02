<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Lesson;

class LessonDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function addLesson(Lesson $lesson)
    {
        $lessonID = -1;

        $sql = 'CALL AddLesson(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $lesson->title,
            $lesson->text,
            $lesson->sectionId
        ]);

        $statement->bindColumn(1, $lessonID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $lessonID;
    }

    public function editLesson(Lesson $lesson)
    {
        $sql = 'CALL EditLesson(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $lesson->id,
            $lesson->title,
            $lesson->text
        ]);
    }

    public function getSectionLessons($sectionID)
    {
        $lessons = [];

        $sql = 'CALL GetSectionLessons(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $sectionID
        ]);

        while($row = $statement->fetch()){
            $lesson = new Lesson();

            $lesson->id = $row['lesson_id'];
            $lesson->title = $row['lesson_title'];
            $lesson->duration = $row['lesson_duration'];
            $lesson->sectionId = $row['section_id'];
            $lesson->courseId = $row['course_id'];

            $lessons[] = $lesson;
        }

        return $lessons;
    }

    public function getLesson($lessonID)
    {
        $lesson = new Lesson();
        
        $sql = 'CALL GetLessonInfo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $lessonID);
        $statement->execute();

        if($row = $statement->fetch()){
            $lesson->id = $row['lesson_id'];
            $lesson->title = $row['lesson_title'];
            $lesson->text = $row['lesson_text'];
            $lesson->duration = $row['lesson_duration'];
            $lesson->sectionId = $row['section_id'];
            $lesson->courseId = $row['course_id'];
            $lesson->extra['videoAddress'] = $row['video_address'];
            return $lesson;
        }
        else{
            return null;
        }
    }

    public function setLessonCompleted($userID, $lessonID, $completed)
    {
        $sql = 'CALL SetLessonCompleted(?, ?, b?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $userID,
            $lessonID,
            $completed
        ]);
    }

    public function getSectionUserLessons($sectionID, $userID)
    {
        $lessons = [];

        $sql = 'CALL GetSectionUserLessons(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $userID,
            $sectionID
        ]);

        while($row = $statement->fetch()){
            $lesson = new Lesson();

            $lesson->id = $row['lesson_id'];
            $lesson->title = $row['lesson_title'];
            $lesson->duration = $row['lesson_duration'];
            $lesson->sectionId = $row['section_id'];
            $lesson->courseId = $row['course_id'];
            $lesson->extra['completed'] = $row['lesson_completed'];

            $lessons[] = $lesson;
        }

        return $lessons;
    }
}