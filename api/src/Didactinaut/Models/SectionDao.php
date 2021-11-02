<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Section;

class SectionDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function addSection(Section $section)
    {
        $sectionID = -1;

        $sql = 'CALL AddSection(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $section->title,
            $section->courseId,
            $section->product['price']
        ]);

        $statement->bindColumn(1, $sectionID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $sectionID;
    }

    public function editSection(Section $section)
    {
        $sql = 'CALL EditSection(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $section->id,
            $section->title,
            $section->product['price']
        ]);
    }

    public function getSection($sectionID)
    {
        $section = new Section();
        
        $sql = 'CALL GetSection(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $sectionID);
        $statement->execute();

        if($row = $statement->fetch()){
            $section->id = $row['section_id'];
            $section->title = $row['section_title'];
            $section->courseId = $row['course_id'];
            $section->product['id'] = $row['product_id'];
            $section->product['price'] = $row['section_price'];
            return $section;
        }
        else{
            return null;
        }
    }

    public function getCourseSections($courseID)
    {
        $sections = [];

        $sql = 'CALL GetCourseSections(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $courseID
        ]);

        while($row = $statement->fetch()){
            $section = new Section();

            $section->id = $row['section_id'];
            $section->title = $row['section_title'];
            $section->courseId = $row['course_id'];
            $section->product['id'] = $row['product_id'];
            $section->product['price'] = $row['section_price'];

            $sections[] = $section;
        }

        return $sections;
    }

    public function getUserCourseSections($courseID, $userID)
    {
        $sections = [];

        $sql = 'CALL GetUserCourseSections(?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $courseID,
            $userID
        ]);

        while($row = $statement->fetch()){
            $section = new Section();

            $section->id = $row['section_id'];
            $section->title = $row['section_title'];
            $section->courseId = $row['course_id'];
            $section->product['id'] = $row['product_id'];
            $section->product['price'] = $row['section_price'];
            $section->extra['accesible'] = $row['user_access'];

            $sections[] = $section;
        }

        return $sections;
    }
}