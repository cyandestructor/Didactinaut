<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Image;

class ImageDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function get($imageId) : ?Image
    {
        $image = new Image();

        $sql = 'CALL GetImage(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $imageId);
        $statement->execute();

        if($row = $statement->fetch()){
            $image->id = $row['image_id'];
            $image->content = $row['image_content'];
            $image->contentType = $row['image_content_type'];
        }
        else{
            return null;
        }

        return $image;
    }
}
