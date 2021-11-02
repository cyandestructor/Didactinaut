<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Resource;

class ResourceDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function addResource(Resource $resource)
    {
        $resourceID = -1;

        $sql = 'CALL AddResource(?, ?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $resource->content,
            $resource->name,
            $resource->contentType,
            $resource->lessonId
        ]);

        $statement->bindColumn(1, $resourceID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $resourceID;
    }

    public function deleteResource($resourceID)
    {
        $sql = 'CALL DeleteResource(?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $resourceID
        ]);
    }

    public function getLessonResources($lessonID)
    {
        $resources = [];

        $sql = 'CALL GetLessonResources(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $lessonID
        ]);

        while($row = $statement->fetch()){
            $resource = new Resource();

            $resource->id = $row['resource_id'];
            $resource->name = $row['resource_name'];
            $resource->contentType = $row['content_type'];

            $resources[] = $resource;
        }

        return $resources;
    }

    public function getResource($resourceID)
    {
        $resource = new Resource();
        
        $sql = 'CALL GetResource(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $resourceID);
        $statement->execute();

        if($row = $statement->fetch()){
            $resource->id = $row['resource_id'];
            $resource->name = $row['resource_name'];
            $resource->content = $row['resource_content'];
            $resource->contentType = $row['content_type'];
            $resource->lessonId = $row['lesson_id'];
            return $resource;
        }
        else{
            return null;
        }
    }
}