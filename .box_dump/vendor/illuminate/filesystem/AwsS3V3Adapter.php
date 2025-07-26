<?php

namespace Illuminate\Filesystem;

use Aws\S3\S3Client;
use Illuminate\Support\Traits\Conditionable;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as S3Adapter;
use League\Flysystem\FilesystemOperator;

class AwsS3V3Adapter extends FilesystemAdapter
{
use Conditionable;






protected $client;









public function __construct(FilesystemOperator $driver, S3Adapter $adapter, array $config, S3Client $client)
{
parent::__construct($driver, $adapter, $config);

$this->client = $client;
}









public function url($path)
{



if (isset($this->config['url'])) {
return $this->concatPathToUrl($this->config['url'], $this->prefixer->prefixPath($path));
}

return $this->client->getObjectUrl(
$this->config['bucket'], $this->prefixer->prefixPath($path)
);
}






public function providesTemporaryUrls()
{
return true;
}









public function temporaryUrl($path, $expiration, array $options = [])
{
$command = $this->client->getCommand('GetObject', array_merge([
'Bucket' => $this->config['bucket'],
'Key' => $this->prefixer->prefixPath($path),
], $options));

$uri = $this->client->createPresignedRequest(
$command, $expiration, $options
)->getUri();




if (isset($this->config['temporary_url'])) {
$uri = $this->replaceBaseUrl($uri, $this->config['temporary_url']);
}

return (string) $uri;
}









public function temporaryUploadUrl($path, $expiration, array $options = [])
{
$command = $this->client->getCommand('PutObject', array_merge([
'Bucket' => $this->config['bucket'],
'Key' => $this->prefixer->prefixPath($path),
], $options));

$signedRequest = $this->client->createPresignedRequest(
$command, $expiration, $options
);

$uri = $signedRequest->getUri();




if (isset($this->config['temporary_url'])) {
$uri = $this->replaceBaseUrl($uri, $this->config['temporary_url']);
}

return [
'url' => (string) $uri,
'headers' => $signedRequest->getHeaders(),
];
}






public function getClient()
{
return $this->client;
}
}
