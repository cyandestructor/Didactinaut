<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Chat;

class ChatDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function createChat(Chat $chat)
    {
        $chatID = -1;

        $sql = 'CALL CreateChat(?, ?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $chat->memberIds[0],
            $chat->memberIds[1],
            $chat->subject
        ]);

        $statement->bindColumn(1, $chatID, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $chatID;
    }

    public function getUserChats($userID)
    {
        $chats = [];

        $sql = 'CALL GetUserChats(?)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $userID
        ]);

        while($row = $statement->fetch()){
            $chat = new Chat();

            $chat->id = $row['chat_id'];
            $chat->subject = $row['chat_subject'];

            $chats[] = $chat;
        }

        return $chats;
    }
}