<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Video;

class VideoDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function addVideo(Video $video)
    {
        $videoID = -1;

        $sql = 'CALL AddVideo(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $video->address,
            $video->duration,
            $video->lessonId
        ]);

        $statement->bindColumn(1, $videoID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $videoID;
    }

    public function editVideo(Video $video)
    {
        $sql = 'CALL EditVideo(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $video->id,
            $video->address,
            $video->duration
        ]);
    }

    public function deleteVideo($videoID)
    {
        $sql = 'CALL DeleteVideo(?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $videoID
        ]);
    }

    public function getVideo($videoID)
    {
        $video = new Video();
        
        $sql = 'CALL GetVideo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $videoID);
        $statement->execute();

        if($row = $statement->fetch()){
            $video->id = $row['video_id'];
            $video->address = $row['video_address'];
            $video->duration = $row['video_duration'];
            $video->lessonId = $row['lesson_id'];
        }
        else{
            return null;
        }
    }

    public function getLessonVideo($lessonID)
    {
        $video = new Video();
        
        $sql = 'CALL GetLessonVideo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $lessonID);
        $statement->execute();

        if($row = $statement->fetch()){
            $video->id = $row['video_id'];
            $video->address = $row['video_address'];
            $video->duration = $row['video_duration'];
            $video->lessonId = $row['lesson_id'];
            return $video;
        }
        else{
            return null;
        }
    }
}