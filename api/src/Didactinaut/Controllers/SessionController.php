<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\User;
use Didactinaut\Models\UserDao;
use Small\Core\Session;

class SessionController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function login(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $data = $request->getParsedBody();

        if(!isset($data['input']) || !isset($data['password'])){
            $response->getBody()->write(json_encode([
                'message' => 'input and password must be specified'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $userDAO = new UserDao($this->dbFactory->create());
        
        $input = $data['input'];
        $password = $data['password'];

        $user = $userDAO->loginUser($input, $password);

        if(!$user){
            $response->getBody()->write(json_encode([
                'message' => 'Incorrect credentials'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);
        }

        $session = new Session();

        $result = [];
        $result['id'] = $user->id;
        $result['username'] = $user->username;
        $result['name'] = $user->name;
        $result['lastname'] = $user->lastname;
        $result['role'] = $user->role;
        $userImage = $user->imageId;
        $result['avatar'] = $userImage ? "/api/images/$userImage" : null;

        $session->set('user', $result);

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    static public function updateUserSession(User $user)
    {
        $session = new Session();

        if($session->exists('user') && $session->get('user')['id'] == $user->id) {
            $data = [];
            $data['id'] = $user->id;
            $data['username'] = $user->username;
            $data['name'] = $user->name;
            $data['lastname'] = $user->lastname;
            $data['role'] = $user->role;
            $userImage = $user->imageId;
            $data['avatar'] = $userImage ? "/api/images/$userImage" : null;

            $session->set('user', $data);
        }
    }

    public function getCurrent(Request $request, Response $response, $args)
    {
        $session = new Session();
        $user = $session->get('user');

        if(!$user){
            return $response
                        ->withStatus(401);
        }

        $response->getBody()->write(json_encode($user));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response, $args)
    {
        Session::destroy();
        return $response;
    }
}
