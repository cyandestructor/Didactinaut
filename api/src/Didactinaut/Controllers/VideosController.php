<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Configuration\VideoUpload\VideoUploaderInterface;
use Didactinaut\Configuration\VideoUpload\Video as VideoUpload;

use Didactinaut\Models\Dto\Video;
use Didactinaut\Models\VideoDao;

class VideosController
{
    private $dbFactory;
    private $videoUploader;

    public function __construct(DatabaseFactoryInterface $dbFactory, VideoUploaderInterface $videoUploader)
    {
        $this->dbFactory = $dbFactory;
        $this->videoUploader = $videoUploader;
    }

    public function putLessonVideo(Request $request, Response $response, $args)
    {
        $lessonID = $request->getAttribute('id');
        
        $contentType = $request->getHeaderLine('Content-Type');

        $result = [];

        $videoContent = $request->getBody();
        if(!$videoContent || $videoContent->getSize() === 0){
            $result['message'] = 'The video file was not specified';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $videoUpload = new VideoUpload();
        
        $videoName = "lesson-$lessonID-video";
        $videoUpload->setName($videoName);
        $videoUpload->setData($videoContent->getContents());
        $videoUpload->setMimeType($contentType);
        $videoUpload->setSize($videoContent->getSize());
        
        $uploadResult = $this->videoUploader->upload($videoUpload);

        if(!$uploadResult){
            return $response
                        ->withStatus(415);
        }

        if($uploadResult['status'] == 'error'){
            $result['code'] = $uploadResult['code'];
            $result['message'] = $uploadResult['message'];
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(500);
        }

        $videoAddress = $uploadResult['url'];

        $videoDAO = new VideoDAO($this->dbFactory->create());
        
        $video = new Video();
        $video->lessonId = $lessonID;
        $video->duration = 0; // TODO: Get the video duration
        $video->address = $videoAddress;

        $result['id'] = $videoDAO->addVideo($video);

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getLessonVideo(Request $request, Response $response, $args)
    {
        $lessonID = $request->getAttribute('id');

        $videoDAO = new VideoDAO($this->dbFactory->create());

        $video = $videoDAO->getLessonVideo($lessonID);

        if(!$video){
            return $response
                        ->withStatus(404);
        }

        $result = [];

        $result['id'] = $video->id;
        $result['address'] = $video->address;
        $result['duration'] = $video->duration;
        $result['lessonId'] = $video->lessonId;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function deleteVideo(Request $request, Response $response, $args)
    {
        $videoID = $request->getAttribute('id');

        $videoDAO = new VideoDAO($this->dbFactory->create());
        $videoDAO->deleteVideo($videoID);

        return $response;
    }
}
