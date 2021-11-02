<?php
namespace Didactinaut\Configuration\VideoUpload;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;

class AzureBlobStorageVideoUploader implements VideoUploaderInterface
{
    public function upload(Video $video)
    {
        $storageAccount = getenv('account-name');
        $container = 'video-container';
        $serviceKey = getenv('account-key');
        $protocol = 'https';
        
        $connectionString =
            "DefaultEndpointsProtocol=$protocol;AccountName=$storageAccount;AccountKey=$serviceKey";

        $name = $video->getName();
        $fileExtension = AzureBlobStorageVideoUploader::getFileExtension($video->getMimeType());
        if(!$fileExtension){
            return false;
        }

        $blobName = $name . $fileExtension;

        $blobClient = BlobRestProxy::createBlobService($connectionString);

        try {
            $blobClient->createBlockBlob($container, $blobName, $video->getData());
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            return ['status' => 'error', 'code' => $code, 'message' => $message];
        } catch (InvalidArgumentTypeException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            return ['status' => 'error', 'code' => $code, 'message' => $message];
        }

        $videoUrl =  $blobClient->getBlobUrl($container, $blobName);

        return ['status' => 'ok', 'url' => $videoUrl];
    }
    
    static private function getFileExtension($mimeType)
    {
        $extensions = [
            'video/mp4' => '.mp4',
            'video/3gpp' => '.3gp',
            'video/quicktime' => '.mov',
            'video/x-msvideo' => '.avi',
            'video/x-ms-wmv' => '.wmv'
        ];

        return $extensions[$mimeType] ?? null;
    }
}