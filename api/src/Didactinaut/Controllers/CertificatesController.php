<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Certificate;
use Didactinaut\Models\CertificateDao;

class CertificatesController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postCertificate(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Certificate creation
        $certificateDAO = new CertificateDAO($this->dbFactory->create());

        $certificate = new Certificate();
        $certificate->user['id'] = $data['userId'];
        $certificate->course['id'] = $data['courseId'];

        $result['id'] = $certificateDAO->registerCertificate($certificate);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $certificateID = $request->getAttribute('id');

        $certificateDAO = new CertificateDAO($this->dbFactory->create());
        $certificate = $certificateDAO->getCertificate($certificateID);

        if(!$certificate){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $certificate->id;
        $result['userName'] = $certificate->user['name'];
        $result['instructorName'] = $certificate->instructor['name'];
        $result['courseTitle'] = $certificate->course['title'];
        $result['expeditionDate'] = $certificate->expeditionDate;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getUserCertificates(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('id');

        $certificateDAO = new CertificateDAO($this->dbFactory->create());

        $certificates = $certificateDAO->getUserCertificates($userID);

        $result = [];
        foreach ($certificates as $certificate) {
            $element = [];

            $element['id'] = $certificate->id;
            $element['courseTitle'] = $certificate->course['title'];

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}