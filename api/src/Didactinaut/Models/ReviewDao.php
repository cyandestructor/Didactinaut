<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Review;

class ReviewDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function createReview(Review $review)
    {
        $reviewID = -1;

        $sql = 'CALL CreateReview(?, ?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $review->body,
            $review->courseId,
            $review->user['id'],
            $review->score
        ]);

        $statement->bindColumn(1, $reviewID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $reviewID;
    }

    public function editReview(Review $review)
    {
        $sql = 'CALL EditReview(?, ?, ?, b?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $review->id,
            $review->body,
            $review->score,
            $review->published
        ]);
    }

    public function getCourseReviews($courseID, $limit, $offset = 0)
    {
        $reviews = [];

        $sql = 'CALL GetCourseReviews(?, ?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $courseID,
            $limit,
            $offset
        ]);

        while($row = $statement->fetch()){
            $review = new Review();

            $review->id = $row['review_id'];
            $review->body = $row['review_body'];
            $review->date = $row['review_date'];
            $review->user['id'] = $row['reviewer_id'];
            $review->user['name'] = $row['reviewer_name'];
            $review->score = $row['review_score'];
            $review->courseId = $row['course_id'];
            $review->published = $row['review_published'];

            $reviews[] = $review;
        }

        return $reviews;
    }

    public function getReview($reviewID)
    {
        $review = new Review();
        
        $sql = 'CALL GetReview(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $reviewID);
        $statement->execute();

        if($row = $statement->fetch()){
            $review->id = $row['review_id'];
            $review->body = $row['review_body'];
            $review->date = $row['review_date'];
            $review->user['id'] = $row['reviewer_id'];
            $review->user['name'] = $row['reviewer_name'];
            $review->score = $row['review_score'];
            $review->courseId = $row['course_id'];
            $review->published = $row['review_published'];
            return $review;
        }
        else{
            return null;
        }
    }
}