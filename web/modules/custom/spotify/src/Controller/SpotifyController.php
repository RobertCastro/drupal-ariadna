<?php

namespace Drupal\spotify\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\GuzzleException;

class SpotifyController extends ControllerBase
{

    protected $client;

    public function __construct()
    {
        $this->client = \Drupal::httpClient();
    }

    private function authorization()
    {

        try {
            $authorization = $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => '64b42797a8e64a37bf06fcd03a6a0968',
                    'client_secret' => '8b068a96e1bd48bc9522ecba85b44c82'
                ]
            ]);

            return $response = json_decode($authorization->getBody());
        } catch (GuzzleException $e) {
            return \Drupal::logger('spotify')->error($e);
        }

    }


    public function getLanzamientos()
    {

        $auth = $this->authorization();

        try {
            $request = $this->client->request('GET', 'https://api.spotify.com/v1/browse/new-releases', [
                'headers' => [
                    'Authorization' => $auth->token_type . ' ' . $auth->access_token
                ]
            ]);

            $lanzamientos = json_decode($request->getBody());
        } catch (GuzzleException $e) {
            return \Drupal::logger('spotify')->error($e);
        }

        $build['lanzamientos'] = [
            '#theme' => 'lanzamientos',
            '#lanzamientos' => $lanzamientos,
        ];

        return $build;

    }


    public function getArtista($id)
    {

        $auth = $this->authorization();

        try {
            $requestArtista = $this->client->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
                'headers' => [
                    'Authorization' => $auth->token_type . ' ' . $auth->access_token
                ]
            ]);

            $Artista = json_decode($requestArtista->getBody());
        } catch (GuzzleException $e) {
            return \Drupal::logger('spotify_cliernt')->error($e);
        }

        try {
            $requestCanciones = $this->client->request('GET', 'https://api.spotify.com/v1/artists/' . $id . '/top-tracks?country=ES', [
                'headers' => [
                    'Authorization' => $auth->token_type . ' ' . $auth->access_token
                ]
            ]);

            $Canciones = json_decode($requestCanciones->getBody());
        } catch (GuzzleException $e) {
            return \Drupal::logger('spotify')->error($e);
        }

        $build['artista'] = [
            '#theme' => 'artista',
            '#artista' => $Artista,
            '#canciones' => $Canciones,
        ];
        return $build;

    }

}