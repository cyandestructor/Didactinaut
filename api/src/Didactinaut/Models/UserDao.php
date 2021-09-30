<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\User;

class UserDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function register(User $user)
    {
        $userID = -1;
        
        $sql = 'CALL RegisterUser(?, ?, ?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);
        
        $statement->execute([
            $user->username,
            $user->name,
            $user->lastname,
            $user->email,
            $hashedPassword
        ]);

        $statement->bindColumn(1, $userID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $userID;
    }

    public function getUser($userID) : ?User
    {
        $user = new User();

        $sql = 'CALL GetUserInfo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $userID);
        $statement->execute();

        if($row = $statement->fetch()){
            $user->id = $row['user_id'];
            $user->username = $row['user_username'];
            $user->name = $row['user_name'];
            $user->lastname = $row['user_lastname'];
            $user->email = $row['user_email'];
            $user->description = $row['user_description'];
            $user->role = $row['user_role'];
            $user->accountCreation = $row['account_creation'];
            $user->accountLastChange = $row['account_last_change'];
            $user->isPublic = $row['user_is_public'];
            $user->imageId = $row['user_image'];
        }
        else{
            return null;
        }

        return $user;
    }

    public function editUser(User $user)
    {
        $sql = 'CALL EditUser(?, ?, ?, ?, ?, ?, ?)';
    
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $user->id,
            $user->username,
            $user->name,
            $user->lastname,
            $user->email,
            $user->description,
            $user->role
        ]);
    }

    public function setImage($userID, $image, $contentType)
    {
        $sql = 'CALL SetUserImage(?, ?, ?)';
    
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $userID,
            $image,
            $contentType
        ]);
    }
    
    public function checkUser($username, $email)
    {
        $count = 0;

        $sql = 'CALL UserExists(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $username,
            $email
        ]);

        $statement->bindColumn(1, $count, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $count;
    }

    public function loginUser($input, $password) : ?User
    {
        $user = new User();
        $sql = 'CALL UserLogin(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $input
        ]);

        if($row = $statement->fetch()){
            $user->id = $row['user_id'];
            $user->username = $row['user_username'];
            $user->role = $row['user_role'];
            $hashedPassword = $row['user_password'];

            if(password_verify($password, $hashedPassword)){
                return $user;
            }
        }
        
        return null;
    }
}