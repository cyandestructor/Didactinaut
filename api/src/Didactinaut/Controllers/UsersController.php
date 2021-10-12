<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\User;
use Didactinaut\Models\UserDao;
use Didactinaut\Validators\UserValidator;
use Didactinaut\Validators\UserEditionValidator;

class UsersController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postUser(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // User data validation
        $validation = new UserValidator($data);
        $errors = $validation->validateForm();
        if (count($errors) > 0) {
            $result['message'] = 'Input is not valid or is incorrect';
            $result['errors'] = $errors;
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        // User register
        $user = new User();
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->gender = $data['gender'];
        $user->birthdate = $data['birthdate'];

        $userDao = new UserDao($this->dbFactory->create());

        $result['id'] = $userDao->register($user);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $result = [];

        $userID = $request->getAttribute('id');

        $userDAO = new UserDao($this->dbFactory->create());
        $user = $userDAO->getUser($userID);
        if(!$user){
            return $response->withStatus(404);
        }
        
        $result['id'] = $user->id;
        $result['username'] = $user->username;
        $result['name'] = $user->name;
        $result['lastname'] = $user->lastname;
        $result['description'] = $user->description;
        $result['email'] = $user->email;
        $result['role'] = $user->role;
        $result['accountCreation'] = $user->accountCreation;
        $result['accountLastChange'] = $user->accountLastChange;
        $result['isPublic'] = (bool)$user->isPublic;
        $result['gender'] = $user->gender;
        $result['birthdate'] = $user->birthdate;
        $userImage = $user->imageId;
        $result['avatar'] = $userImage ? "/api/images/$userImage" : null;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function putUser(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response->withStatus(415);
        }

        $result = [];

        $userID = $request->getAttribute('id');

        $userDAO = new UserDao($this->dbFactory->create());
        // Get the original user info
        $user = $userDAO->getUser($userID);

        if(!$user){
            $result['message'] = 'User does not exists';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }

        // Get the data to edit and validate
        $data = $request->getParsedBody();

        $validation = new UserEditionValidator($data);
        $errors = $validation->validateForm();
        if (count($errors) > 0) {
            $result['message'] = 'Input is not valid or is incorrect';
            $result['errors'] = $errors;
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        // Join the data to be sent to the database
        $editedUser = new User();
        $editedUser->id = $userID;
        $editedUser->username = $data['username'] ?? $user->username;
        $editedUser->name = $data['name'] ?? $user->name;
        $editedUser->lastname = $data['lastname'] ?? $user->lastname;
        $editedUser->email = $data['email'] ?? $user->email;
        $editedUser->description = $data['description'] ?? $user->description;
        $editedUser->role = $data['role'] ?? $user->role;
        $editedUser->gender = $data['gender'] ?? $user->gender;
        $editedUser->birthdate = $data['birthdate'] ?? $user->birthdate;

        $userDAO->editUser($editedUser);

        // Return the old and new data
        $result['old'] = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'description' => $user->description,
            'role' => $user->role,
            'gender' => $user->gender,
            'birthdate' => $user->birthdate
        ];

        $result['new'] = [
            'id' => $editedUser->id,
            'username' => $editedUser->username,
            'name' => $editedUser->name,
            'lastname' => $editedUser->lastname,
            'email' => $editedUser->email,
            'description' => $editedUser->description,
            'role' => $editedUser->role,
            'gender' => $editedUser->gender,
            'birthdate' => $editedUser->birthdate
        ];

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function putUserImage(Request $request, Response $response, $args)
    {
        $result = [];
        
        $supportedMediaTypes = ['image/jpeg', 'image/png'];
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || !in_array($contentType, $supportedMediaTypes)){
            return $response
                        ->withStatus(415);
        }

        $userID = $request->getAttribute('id');

        // TODO: Check if the user exists
        $avatar = $request->getBody();
        if(!$avatar || $avatar->getSize() === 0){
            $result['message'] = 'The avatar file was not specified';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $userDAO = new UserDao($this->dbFactory->create());
        $userDAO->setImage($userID, $avatar, $contentType);

        return $response;
    }

    public function checkUserExists(Request $request, Response $response, $args)
    {
        $queryParams = $request->getQueryParams();

        $username = $queryParams['username'] ?? '';
        $email = $queryParams['email'] ?? '';

        $userDAO = new UserDao($this->dbFactory->create());

        $count = $userDAO->checkUser($username, $email);

        if($count <= 0){
            return $response
                        ->withStatus(404);
        }

        return $response;
    }
}
