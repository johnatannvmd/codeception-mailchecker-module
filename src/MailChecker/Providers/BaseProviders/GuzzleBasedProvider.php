<?php
namespace MailChecker\Providers\BaseProviders;

use GuzzleHttp\Client;

trait GuzzleBasedProvider
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $transport;

    /**
     * GuzzleBasedProvider constructor.
     *
     * @param array $config
     *
     * @see \GuzzleHttp\RequestOptions for a list of available request options.
     */
    public function __construct(array $config)
    {
        $clientConfig = [
            'base_uri' => $config['options']['url'] . ':' . $config['options']['port']
        ];

        if (isset($config['options'], $config['options']['guzzleOptions'])) {
            $clientConfig = array_merge($clientConfig, $config['options']['guzzleOptions']);
        }

        $this->transport = new Client($clientConfig);
    }
}
