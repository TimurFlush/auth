<?php

use Phalcon\Http\Request as PhalconRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends PhalconRequest
{
    /**
     * GET params
     *
     * @var $query
     */
    protected $query;

    /**
     * POST params
     *
     * @var $request
     */
    protected $request;

    protected $attributes;

    protected $cookies;

    protected $files;

    protected $server;

    protected $content;

    protected $headers;

    /**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     */
    public function __construct($query = null, $request = null, $attributes = null, $cookies = null, $files = null, $server = null, $headers = null)
    {
        $this->query = $query ?? $_GET;
        $this->request = $request ?? $_POST;
        $this->request = $attributes;
        //this->cookies = $cookies;
        $this->files = $files;
        $this->headers = $this->prepareHeaders(
            $headers ?? $server,
            $headers === null
        );
    }

    protected function prepareHeaders(array $headers, bool $fromServer)
    {

    }

    public function get(
        string $name = null,
        $filters = null,
        $defaultValue = null,
        bool $notAllowEmpty = false,
        bool $noRecursive = false
    ) {
        return $this->getHelper(
            array_merge($this->query, $this->request, $this->attributes),
            $name,
            $filters,
            $defaultValue,
            $notAllowEmpty,
            $noRecursive
        );
    }

    public function getPost(
        string $name = null,
        $filters = null,
        $defaultValue = null,
        bool $notAllowEmpty = false,
        bool $noRecursive = false
    ) {
        return $this->getHelper(
            $this->request,
            $name,
            $filters,
            $defaultValue,
            $notAllowEmpty,
            $noRecursive
        );
    }

    public function getQuery(
        string $name = null,
        $filters = null,
        $defaultValue = null,
        bool $notAllowEmpty = false,
        bool $noRecursive = false
    ) {
        return $this->getHelper(
            $this->query,
            $name,
            $filters,
            $defaultValue,
            $notAllowEmpty,
            $noRecursive
        );
    }

    public function getScheme(): string
    {
        $http = $this->getServer('HTTPS');

        if ($http === 'on' || $http == 1) {
            return 'https';
        }

        return 'http';
    }

    public function getUploadedFiles(bool $onlySuccessful = false, bool $namedKeys = false): array
    {
        $files = [];

        if (count($this->files)) {
            foreach ($this->files as $prefix => $input) {
                if (is_array($input['name'])) {
                    $smoothInput = $this->smoothFiles(
                        $input['name'],
                        $input['type'],
                        $input['tmp_name'],
                        $input['size'],
                        $input['error'],
                        $prefix
                    );

                    foreach ($smoothInput as $file) {
                        if ($onlySuccessful === false || $file['error'] === UPLOAD_ERR_OK) {
                            $dataFile = [
                                'name' => $file['name'],
                                'type' => $file['type'],
                                'tmp_name' => $file['tmp_name'],
                                'size' => $file['size'],
                                'error' => $file['error']
                            ];

                            if ($namedKeys === true) {
                                $files[$file['key']] = new PhalconRequest\File(
                                    $dataFile,
                                    $file['key']
                                );
                            } else {
                                $files[] = new PhalconRequest\File(
                                    $dataFile,
                                    $file['key']
                                );
                            }
                        }
                    }
                } else {
                    if ($onlySuccessful === false || $input['error'] === UPLOAD_ERR_OK) {
                        if ($namedKeys === true) {
                            $files[$prefix] = new PhalconRequest\File($input, $prefix);
                        } else {
                            $files[] = new PhalconRequest\File($input, $prefix);
                        }
                    }
                }
            }
        }

        return $files;
    }

    public function has(string $name): bool
    {
        $union = array_merge(
            $this->query,
            $this->request,
            $this->attributes
        );

        return isset($union[$name]);
    }

    public function hasPost(string $name): bool
    {
        return isset($this->request[$name]);
    }

    public function hasQuery(string $name): bool
    {
        return isset($this->query[$name]);
    }

    public function numFiles(bool $onlySuccessful = false): int
    {
        $numberFiles = 0;

        if (is_array($this->files)) { // TODO : Remove if need
            return $numberFiles;
        }

        foreach ($this->files as $file) {
            if (isset($file['error'])) {
                $error = $file['error'];

                if (is_array($error)) {
                    $numberFiles += $this->hasFileHelper($error, $onlySuccessful);
                } elseif (!$error || !$onlySuccessful) {
                    $numberFiles++;
                }
            }
        }

        return $numberFiles;
    }

    /**
     * ATTENTION : In original class it's the private method
     */
    protected function getServerArray(): array
    {
        return $this->server;
    }

    public static function createFromServerRequest(ServerRequestInterface $serverRequest)
    {
        $server = [];
        $uri = $serverRequest->getUri();

        if ($uri instanceof UriInterface) {
            $server['SERVER_NAME'] = $uri->getHost();
            $server['SERVER_PORT'] = $uri->getPort();
            $server['REQUEST_URI'] = $uri->getPath();
            $server['QUERY_STRING'] = $uri->getQuery();
        }

        $server['REQUEST_METHOD'] = $serverRequest->getMethod();

        $server = array_merge($server, $serverRequest->getServerParams());

        $parsedBody = $serverRequest->getParsedBody();
        $parsedBody = is_array($parsedBody)
            ? $parsedBody
            : [];

        $request = new Request(
            $serverRequest->getQueryParams(),
            $parsedBody,
            $serverRequest->getAttributes(),
            $serverRequest->getCookieParams(),
            $this->getFiles($serverRequest->getUploadedFiles()),
            $server,
            $streamed ? $serverRequest->getBody()->detach() : $serverRequest->getBody()->__toString()
        );

        $request->headers->replace($serverRequest->getHeaders());

        return $request;
    }
}
