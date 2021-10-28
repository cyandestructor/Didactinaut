<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Category;

class CategoryDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function create(Category $category)
    {
        $categoryId = -1;
        
        $sql = 'CALL CreateCategory(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $category->name,
            $category->description
        ]);

        $statement->bindColumn(1, $categoryId, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $categoryId;
    }

    public function getCategory($categoryId) : ?Category
    {
        $category = new Category();

        $sql = 'CALL GetCategory(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $categoryId);
        $statement->execute();

        if($row = $statement->fetch()){
            $category->id = $row['category_id'];
            $category->name = $row['category_name'];
            $category->description = $row['category_description'];
        }
        else{
            return null;
        }

        return $category;
    }

    public function getCategories()
    {
        $categories = [];

        $sql = 'CALL GetCategories()';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        while($row = $statement->fetch()){
            $category = new Category();

            $category->id = $row['category_id'];
            $category->name = $row['category_name'];
            $category->description = $row['category_description'];

            $categories[] = $category;
        }

        return $categories;
    }

    public function getCourseCategories($courseID)
    {
        $categories = [];

        $sql = 'CALL GetCourseCategories(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $courseID
        ]);

        while($row = $statement->fetch()){
            $category = new Category();

            $category->id = $row['category_id'];
            $category->name = $row['category_name'];
            $category->description = $row['category_description'];

            $categories[] = $category;
        }

        return $categories;
    }
}