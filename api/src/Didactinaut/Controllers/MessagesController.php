<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Message;
use Didactinaut\Models\MessageDao;

class MessagesController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postMessage(Request $request, Response $response, $args)
    {
        $chatID = $request->getAttribute('id');

        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Message creation
        $messageDAO = new MessageDAO($this->dbFactory->create());

        $message = new Message();
        $message->senderId = $data['senderId'];
        $message->chatId = $chatID;
        $message->body = $data['body'];

        $result['id'] = $messageDAO->createMessage($message);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function getChatMessages(Request $request, Response $response, $args)
    {
        $chatID = $request->getAttribute('id');

        $messageDAO = new MessageDAO($this->dbFactory->create());

        $messages = $messageDAO->getChatMessages($chatID);

        $result = [];
        foreach ($messages as $message) {
            $element = [];

            $element['id'] = $message->id;
            $element['body'] = $message->body;
            $element['date'] = $message->date;
            $element['senderId'] = $message->senderId;
            $element['senderName'] = $message->senderName;
            $element['chatId'] = $message->chatId;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}