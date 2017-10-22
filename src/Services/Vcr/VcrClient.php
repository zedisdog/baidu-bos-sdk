<?php
/*
* Copyright 2015 Baidu, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace BaiduBce\Services\Vcr;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\DateUtils;

class VcrClient extends BceBaseClient
{

    private $signer;
    private $httpClient;
    private $prefix = '/v1';

    /**
     * The VcrClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'VcrClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Check a video with midiaId.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          preset: string, analyze preset name
     *          notification: string, notification name
     *      }
     * @return nothing
     * @throws BceClientException
     */
    public function check($mediaId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Get result of video checking.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed video checking detail
     * @throws BceClientException
     */
    public function getCheckingResult($mediaId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Get porn checking result of video.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return video porn checking detail
     * @throws BceClientException
     */
    public function getPornResult($mediaId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }
        $params = array(
            'porn' => null,
        );

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Get ad checking result of video.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return video porn checking detail
     * @throws BceClientException
     */
    public function getAdResult($mediaId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }
        $params = array(
            'ad' => null,
        );

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Get terrorism checking result of video.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return video porn checking detail
     * @throws BceClientException
     */
    public function getTerrorismResult($mediaId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }
        $params = array(
            'terrorism' => null,
        );

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Get a preset.
     *
     * @param $name string, preset name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed preset detail
     * @throws BceClientException
     */
    public function getPreset($name, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/preset/$name"
        );
    }

    /**
     * List presets.
     *
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @param array $options
     * @return mixed
     */
    public function listPresets($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            '/preset'
        );
    }

    /**
     * Create HttpClient and send request
     * @param string $httpMethod The Http request method
     * @param array $varArgs The extra arguments
     * @param string $requestPath The Http request uri
     * @return mixed The Http response and headers.
     */
    private function sendRequest($httpMethod, array $varArgs, $requestPath = '/')
    {
        $defaultArgs = array(
            'config' => array(),
            'body' => null,
            'headers' => array(),
            'params' => array(),
        );

        $args = array_merge($defaultArgs, $varArgs);
        if (empty($args['config'])) {
            $config = $this->config;
        } else {
            $config = array_merge(
                array(),
                $this->config,
                $args['config']
            );
        }
        if (!isset($args['headers'][HttpHeaders::CONTENT_TYPE])) {
            $args['headers'][HttpHeaders::CONTENT_TYPE] = HttpContentTypes::JSON;
        }
        $path = $this->prefix . $requestPath;
        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $path,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer
        );

        $result = $this->parseJsonResult($response['body']);

        return $result;
    }
}
