<?php

namespace App\Services;

use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToCheckDirectoryExistence;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToListContents;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;

class GoogleDriveAdapter implements FilesystemAdapter
{
    private Drive $service;
    private PathPrefixer $prefixer;
    private string $root;

    public function __construct(Drive $service, string $root = 'root')
    {
        $this->service = $service;
        $this->root = $root;
        $this->prefixer = new PathPrefixer($root);
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->getFileByPath($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        try {
            $this->getFolderByPath($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $pathInfo = pathinfo($path);
        $parentId = $this->getFolderByPath($pathInfo['dirname'] ?? '');
        
        $file = new DriveFile();
        $file->setName($pathInfo['basename']);
        $file->setParents([$parentId]);

        try {
            $this->service->files->create($file, [
                'data' => $contents,
                'mimeType' => $this->getMimeType($path),
                'uploadType' => 'multipart'
            ]);
        } catch (\Exception $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        try {
            $file = $this->getFileByPath($path);
            $response = $this->service->files->get($file->getId(), ['alt' => 'media']);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function readStream(string $path)
    {
        $contents = $this->read($path);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);
        return $stream;
    }

    public function delete(string $path): void
    {
        try {
            $file = $this->getFileByPath($path);
            $this->service->files->delete($file->getId());
        } catch (\Exception $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function deleteDirectory(string $path): void
    {
        try {
            $folder = $this->getFolderByPath($path);
            $this->service->files->delete($folder->getId());
        } catch (\Exception $e) {
            throw UnableToDeleteDirectory::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        $pathInfo = pathinfo($path);
        $parentId = $this->getFolderByPath($pathInfo['dirname'] ?? '');
        
        $folder = new DriveFile();
        $folder->setName($pathInfo['basename']);
        $folder->setMimeType('application/vnd.google-apps.folder');
        $folder->setParents([$parentId]);

        try {
            $this->service->files->create($folder);
        } catch (\Exception $e) {
            throw UnableToCreateDirectory::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Google Drive does not support visibility settings');
    }

    public function visibility(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::visibility($path, 'Google Drive does not support visibility settings');
    }

    public function mimeType(string $path): FileAttributes
    {
        try {
            $file = $this->getFileByPath($path);
            return new FileAttributes($path, null, null, null, $file->getMimeType());
        } catch (\Exception $e) {
            throw UnableToRetrieveMetadata::mimeType($path, $e->getMessage(), $e);
        }
    }

    public function lastModified(string $path): FileAttributes
    {
        try {
            $file = $this->getFileByPath($path);
            $timestamp = strtotime($file->getModifiedTime());
            return new FileAttributes($path, null, null, $timestamp);
        } catch (\Exception $e) {
            throw UnableToRetrieveMetadata::lastModified($path, $e->getMessage(), $e);
        }
    }

    public function fileSize(string $path): FileAttributes
    {
        try {
            $file = $this->getFileByPath($path);
            return new FileAttributes($path, (int) $file->getSize());
        } catch (\Exception $e) {
            throw UnableToRetrieveMetadata::fileSize($path, $e->getMessage(), $e);
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        try {
            $folderId = $path === '' ? $this->root : $this->getFolderByPath($path);
            
            $query = "'{$folderId}' in parents and trashed=false";
            $results = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name,mimeType,size,modifiedTime,parents)'
            ]);

            foreach ($results->getFiles() as $file) {
                $filePath = $path === '' ? $file->getName() : $path . '/' . $file->getName();
                
                if ($file->getMimeType() === 'application/vnd.google-apps.folder') {
                    yield new DirectoryAttributes($filePath);
                    
                    if ($deep) {
                        yield from $this->listContents($filePath, true);
                    }
                } else {
                    yield new FileAttributes(
                        $filePath,
                        (int) $file->getSize(),
                        null,
                        strtotime($file->getModifiedTime()),
                        $file->getMimeType()
                    );
                }
            }
        } catch (\Exception $e) {
            throw UnableToListContents::atLocation($path, $deep, $e);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $file = $this->getFileByPath($source);
            $destinationInfo = pathinfo($destination);
            $newParentId = $this->getFolderByPath($destinationInfo['dirname'] ?? '');
            
            $updatedFile = new DriveFile();
            $updatedFile->setName($destinationInfo['basename']);
            
            $this->service->files->update($file->getId(), $updatedFile, [
                'addParents' => $newParentId,
                'removeParents' => implode(',', $file->getParents())
            ]);
        } catch (\Exception $e) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $e);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $sourceFile = $this->getFileByPath($source);
            $destinationInfo = pathinfo($destination);
            $parentId = $this->getFolderByPath($destinationInfo['dirname'] ?? '');
            
            $copiedFile = new DriveFile();
            $copiedFile->setName($destinationInfo['basename']);
            $copiedFile->setParents([$parentId]);
            
            $this->service->files->copy($sourceFile->getId(), $copiedFile);
        } catch (\Exception $e) {
            throw UnableToWriteFile::atLocation($destination, $e->getMessage(), $e);
        }
    }

    private function getFileByPath(string $path): DriveFile
    {
        if ($path === '') {
            throw new \InvalidArgumentException('Path cannot be empty');
        }

        $parts = explode('/', trim($path, '/'));
        $parentId = $this->root;
        
        foreach ($parts as $i => $part) {
            $isLast = $i === count($parts) - 1;
            $query = "name='{$part}' and '{$parentId}' in parents and trashed=false";
            
            if (!$isLast) {
                $query .= " and mimeType='application/vnd.google-apps.folder'";
            }
            
            $results = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name,mimeType,size,modifiedTime,parents)'
            ]);
            
            $files = $results->getFiles();
            if (empty($files)) {
                throw new \Exception("File or folder not found: {$part}");
            }
            
            $file = $files[0];
            if ($isLast) {
                return $file;
            }
            
            $parentId = $file->getId();
        }
        
        throw new \Exception("File not found: {$path}");
    }

    private function getFolderByPath(string $path): string
    {
        if ($path === '' || $path === '.') {
            return $this->root;
        }

        $parts = explode('/', trim($path, '/'));
        $parentId = $this->root;
        
        foreach ($parts as $part) {
            $query = "name='{$part}' and '{$parentId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            $results = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id)'
            ]);
            
            $files = $results->getFiles();
            if (empty($files)) {
                throw new \Exception("Folder not found: {$part}");
            }
            
            $parentId = $files[0]->getId();
        }
        
        return $parentId;
    }

    private function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
