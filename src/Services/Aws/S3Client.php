<?php namespace Core\Services\Aws;

use Aws\S3\S3Client as Client;
use Aws\S3\Exception\S3Exception;
use Log;
     
class S3Client
{
    private $s3                     = null;
    private $bucket                 = null;
    private $contentType            = null;
    private $mediaUrl               = null;       
    const ACL_PRIVATE               = 'private';
    const ACL_PUBLIC_READ           = 'public-read';
    const ACL_PUBLIC_READ_WRITE     = 'public-read-write';
    const ACL_AUTHENTICATED_READ    = 'authenticated-read';
    const ACL_BUCKET_OWNER_READ     = 'bucket-owner-read';
    const ACL_BUCKET_OWNER_FULL     = 'bucket-owner-full-control';    
    const STORAGE_CLASS_STANDARD    = 'STANDARD';
    const STORAGE_CLASS_RRS         = 'REDUCED_REDUNDANCY';
    const SSE_NONE                  = '';
    const SSE_AES256                = 'AES256';
     
    public function __construct($config = null)
    { 
        if (empty($config)) {
            $config = config('services.aws');
        }
        $this->s3 = new Client([
            'key'           => $config['access_key_id'],
            'secret'        => $config['secret_key'],
            'region'        => $config['region'],
            'version'       => $config['version']
        ]);
        $this->bucket       = $config['media_bucket'];
        $this->contentType  = 'text/plain';
        $this->mediaUrl     = $config['url'].'/'.$this->bucket;
    }
    
    public function put($path, $key, $metadata = [])
    {   
        try {         
            $result = $this->s3->putObject(array(
                'Bucket'       => $this->bucket,
                'Key'          => $key,
                'SourceFile'   => $path,
                //'Body'         => fopen('/path/to/file', 'r'),
                'ContentType'  => 'image/jpeg',
                'ACL'          => 'public-read-write',
                'CacheControl' => 'max-age=31536000',
                'StorageClass' => 'REDUCED_REDUNDANCY',
                'Metadata'     => $metadata
            ));                        
            Log::debug(
                'Amazon S3 Put', [
                    'Bucket'       => $this->bucket,
                    'Key'          => $key,
                    'SourceFile'   => $path,
                    'ContentType'  => $this->contentType,
                    'ACL'          => 'public-read-write',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                    'ObjectURL'    => $result['ObjectURL'],
                    'Metadata'     => $metadata
            ]);
            return $result;
        } catch (S3Exception $e) {}  
    }

    public function copy($source, $target, $metadata = [])
    {
        try {
            $result = $this->s3->copyObject([
                'Bucket'       => $this->bucket,    // target bucket
                'Key'          => $target,
                'CopySource'   => $this->bucket . '/' . $source,
                'ContentType'  => $this->contentType,
                'ACL'          => 'public-read',
                'CacheControl' => 'max-age=31536000',
                'StorageClass' => 'REDUCED_REDUNDANCY'//,
                'Metadata'     => $metadata
            ]);
            Log::debug(
                __FILE__ . ': Amazon S3 copy', 
                [
                    'Bucket'       => $this->bucket,
                    'Key'          => $target,
                    'CopySource'   => $this->bucket . '/' . $source,
                    'ContentType'  => $this->contentType,
                    'ACL'          => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                    'Metadata'     => $metadata
                ],
                'amazon'
            );
            return $result;
        
        } catch (S3Exception $e) {}    
    }
        
    public function get($key, $path='/tmp')
    { 
        try {
            $result = $this->s3->getObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
                'SaveAs' => $path . DS . $key
            ]);            
            Log::debug(
                __FILE__ . ': Amazon S3 get', 
                [
                    'Bucket' => $this->bucket,
                    'Key'    => $key,
                    'SaveAs' => $path . DS . $key,
                    'result' => $result
                ],
                'amazon'
            );
            return $result;
        
        } catch (S3Exception $e) {}   
    }

    public function getMetadata($key)
    {
		try {
               $result = $s3->headObject([
				'Bucket' => $this->bucket,
				'Key'    => $key
			]);
        	     return $result['Metadata'];
		} catch (S3Exception $e) {
		    echo "Error retrieving metadata.\n";
		}
    }
     
    public function delete($key)
    {
        $this->s3->deleteObject([
            'Bucket'       => $this->bucket,
            'Key'          => $key
        ]);
    }
            
    public function isExist($key)
    { 
        try {
            $result = $this->s3->doesObjectExist($this->bucket, $key);
            return $result; 
        } catch (S3Exception $e) {
        
        }   
    }

    public function registerStreamWrapper()
    {
        $this->s3->registerStreamWrapper();
    }
        
    public function setBucket($bucket)
    {
        return $this->bucket = $bucket;
    }

    public function getBucket()
    {
        return $this->bucket;
    }
        
    public function setContentType($type)
    {
        return $this->contentType = $type;
    } 
    
    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }
}
