<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Chat;
use Didactinaut\Models\ChatDao;

class ChatsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postChat(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('id');
        
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Chat creation
        $chatDAO = new ChatDAO($this->dbFactory->create());

        $chat = new Chat();
        $chat->memberIds[0] = $userID;
        $chat->memberIds[1] = $data['receptorId'];
        $chat->subject = $data['subject'];

        $result['id'] = $chatDAO->createChat($chat);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function getUserChats(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('id');

        $chatDAO = new ChatDAO($this->dbFactory->create());

        $chats = $chatDAO->getUserChats($userID);

        $result = [];
        foreach ($chats as $chat) {
            $element = [];

            $element['id'] = $chat->id;
            $element['subject'] = $chat->subject;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}