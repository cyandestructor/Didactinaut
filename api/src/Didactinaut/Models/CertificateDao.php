<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Certificate;

class CertificateDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function registerCertificate(Certificate $certificate)
    {
        $certificateID = null;

        $sql = 'CALL RegisterCertificate(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $certificate->user['id'],
            $certificate->course['id']
        ]);

        $statement->bindColumn(1, $certificateID);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $certificateID;
    }

    public function getCertificate($certificateID)
    {
        $certificate = new Certificate();
        
        $sql = 'CALL GetCertificate(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $certificateID);
        $statement->execute();

        if($row = $statement->fetch()){
            $certificate->id = $row['certificate_id'];
            $certificate->user['name'] = $row['user_fullname'];
            $certificate->instructor['name'] = $row['instructor_fullname'];
            $certificate->course['title'] = $row['course_title'];
            $certificate->expeditionDate = $row['expedition_date'];
            return $certificate;
        }
        else{
            return null;
        }
    }

    public function getUserCertificates($userID)
    {
        $certificates = [];

        $sql = 'CALL GetUserCertificates(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $userID
        ]);

        while($row = $statement->fetch()){
            $certificate = new Certificate();

            $certificate->id = $row['certificate_id'];
            $certificate->course['title'] = $row['course_title'];

            $certificates[] = $certificate;
        }

        return $certificates ;
    }
}