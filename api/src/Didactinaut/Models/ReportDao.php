<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;

class ReportDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function getInstructorSalesReport($instructorId)
    {
        $report = [];
        
        $coursesSalesReport = $this->getInstructorSalesReportA($instructorId);
        $paymentMethodsReport = $this->getInstructorSalesReportB($instructorId);

        $report['coursesSalesReport'] = $coursesSalesReport;
        $report['paymentMethodsReport'] = $paymentMethodsReport;

        return $report;
    }

    public function getInstructorSalesReportA($instructorId)
    {
        $report = [];
        $sql = 'CALL InstructorReportA(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $instructorId
        ]);

        while($row = $statement->fetch()){
            $element = [];

            $element['courseId'] = $row['course_id'];
            $element['courseTitle'] = $row['course_title'];
            $element['totalStudents'] = $row['total_students'];
            $element['completionAverage'] = $row['average_completion'];
            $element['totalSales'] = $row['total_sales'];

            $report[] = $element;
        }

        return $report;
    }

    public function getInstructorSalesReportB($instructorId)
    {
        $report = [];
        $sql = 'CALL InstructorReportB(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $instructorId
        ]);

        while($row = $statement->fetch()){
            $element = [];

            $element['paymentMethod'] = $row['payment_method'];
            $element['totalSales'] = $row['total_sales'];

            $report[] = $element;
        }

        return $report;
    }

    public function getCourseUsersReport($courseId)
    {
        $report = [];
        $sql = 'CALL CourseReportA(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $courseId
        ]);

        while($row = $statement->fetch()){
            $user = [];

            $user['fullname'] = $row['user_fullname'];
            $user['enrollDate'] = $row['enroll_date'];
            $user['completionRatio'] = $row['completion_ratio'];
            $user['totalPaid'] = $row['total_paid'];
            $user['paymentMethod'] = $row['payment_method'];

            $report[] = $user;
        }

        return $report;
    }
}