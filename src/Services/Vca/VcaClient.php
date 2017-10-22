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

namespace BaiduBce\Services\Vca;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\DateUtils;

class VcaClient extends BceBaseClient
{

    private $signer;
    private $httpClient;
    private $prefix = '/v1';

    /**
     * The VcaClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'VcaClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Analyze a video with midiaId.
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
    public function analyze($mediaId, $options = array())
    {
        list($config, $preset, $notification) = $this->parseOptions(
            $options,
            'config',
            'preset',
            'notification'
        );

        if (empty($mediaId)) {
            throw new BceClientException("The parameter mediaId "
                . "should NOT be null or empty string");
        }

        $body = array();

        if ($preset !== null) {
            $body['preset'] = $preset;
        }
        if ($notification !== null) {
            $body['notification'] = $notification;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/media/$mediaId"
        );
    }

    /**
     * Query result of video analyzation.
     *
     * @param $mediaId string, mediaId of video
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed video analyzation detail
     * @throws BceClientException
     */
    public function queryAnalyzation($mediaId, $options = array())
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
     * Create a notification.
     *
     * @param $name string, notification name
     * @param $endpoint string, notification endpoint
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed notification detail
     * @throws BceClientException
     */
    public function createNotification($name, $endpoint, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }
        if (empty($endpoint)) {
            throw new BceClientException("The parameter endpoint "
                . "should NOT be null or empty string");
        }

        $body = array(
            'name' => $name,
            'endpoint' => $endpoint,
        );

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            '/notification'
        );
    }

    /**
     * Get a notification.
     *
     * @param $name string, notification name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed notification detail
     * @throws BceClientException
     */
    public function getNotification($name, $options = array())
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
            "/notification/$name"
        );
    }

    /**
     * List notifications.
     *
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return notification list
     * @throws BceClientException
     */
    public function listNotifications($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/notification"
        );
    }

    /**
     * Create a preset.
     *
     * @param $name string, preset name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          isDefault: boolean, create as default preset
     *          asr: {
     *              enabled: boolean, enable asr analyzation
     *          },
     *          ocr: {
     *              enabled: boolean, enable ocr analyzation
     *          },
     *          scenarioTag: {
     *              enabled: boolean, enable scenario tag analyzation
     *          },
     *          scenarioClassify: {
     *              enabled: boolean, enable scenario classify analyzation
     *          },
     *          imageClassify: {
     *              enabled: boolean, enable image classify analyzation
     *          },
     *          faceRecognition: {
     *              enabled: boolean, enable face recognition analyzation
     *          },
     *          asrTagEnable: boolean, enable tag from asr
     *          ocrTagEnable: boolean, enable tag from ocr
     *      }
     * @return mixed
     * @throws BceClientException
     */
    public function createPreset($name, $options = array())
    {
        list($config, $asr, $ocr, $scenarioTag, $scenarioClassify, $imageClassify,
            $faceRecognition, $asrTagEnable, $ocrTagEnable) = $this->parseOptions(
            $options,
            'config',
            'asr',
            'ocr',
            'scenarioTag',
            'scenarioClassify',
            'imageClassify',
            'faceRecognition',
            'asrTagEnable',
            'ocrTagEnable'
        );

        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }

        $body = array(
            'name' => $name,
        );

        if ($asr !== null) {
            $body['asr'] = $asr;
        }
        if ($ocr !== null) {
            $body['ocr'] = $ocr;
        }
        if ($scenarioTag !== null) {
            $body['scenarioTag'] = $scenarioTag;
        }
        if ($scenarioClassify !== null) {
            $body['scenarioClassify'] = $scenarioClassify;
        }
        if ($imageClassify !== null) {
            $body['imageClassify'] = $imageClassify;
        }
        if ($faceRecognition !== null) {
            $body['faceRecognition'] = $faceRecognition;
        }
        if ($asrTagEnable !== null) {
            $body['asrTagEnable'] = $asrTagEnable;
        }
        if ($ocrTagEnable !== null) {
            $body['ocrTagEnable'] = $ocrTagEnable;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            '/preset'
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
