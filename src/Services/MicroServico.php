<?php

namespace Gsferro\MicroServico\Services;

use Ixudra\Curl\Facades\Curl;

class MicroServico
{
    private $link;
    private $extraHeader = [];

    /**
     * @param array $extraHeader
     */
    public function setExtraHeader(array $extraHeader): MicroServico
    {
        $this->extraHeader = $extraHeader;
        return $this;
    }

    private function valide($api)
    {
        $link = $this->link($api);
        if (is_null($link)) {
            $res = [
                'data'      => [],
                'success'   => false,
                'message'   => "API {$api} não encontrada ou liberada para uso!",
            ];
            return response()->json($res, 404);
        }

        $this->link = $link;
    }

    private function link($api)
    {
        return config("microservico.{$api}");
    }

    private function curl($url)
    {
        return Curl::to($url)
            ->withHeaders(array_merge(
                [
                    "accept"          => "application/json",
                    "accept-language" => "en-US,en;q=0.8",
                ], $this->extraHeader))
            ->asJsonResponse();
    }

    /*private function apiException($message)
    {
        $res = [
            'data'      => [],
            'success'   => false,
            'message'   => $message,
        ];
        return response()->json($res, 404);
    }*/

    /**
     * Efetua consulta utilizadno VERBO HTTP GET
     * @param string $apis
     * @param string $params
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(string $api, string $params = null)
    {
        //        try {
        $this->valide($api);
        $url = $this->link . (!empty($params) ? "/{$params}" : "");

        return $this->curl($url)
            ->get();
        //        } catch (\Exception $e) {
        //            return $this->apiException($e->getMessage());
        //        }
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP POST
     * @param string $apis
     * @param array $dados
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function post($api, $dados)
    {
        $this->valide($api);

        $url = $this->link . (!empty($params) ? "/{$params}" : "");

        return Curl::to($url)
            ->withData($dados)
            ->withHeaders(
                [
                    "accept" => "*/*",
                    "accept-language" => "en-US,en;q=0.8",
                ]
            )
            ->asJson(true)
            ->withContentType('application/json')
            ->asJsonResponse()
            ->post();
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP PUT
     * @param string $apis
     * @param string $params
     * @param array $dados
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function put($api, $params = '', $dados)
    {

        $link = self::link($api);
        if (is_null($link)) {
            $res = [
                'data'      => [],
                'success'   => false,
                'message'   => "API {$api} não encontrada ou liberada para uso!",
            ];
            return response()->json($res, 404);
        }

        $url = $link . (!empty($params) ? "/{$params}" : "");

        return Curl::to($url)
            ->withData($dados)
            ->withHeaders(
                [
                    "accept" => "*/*",
                    "accept-language" => "en-US,en;q=0.8",
                ]
            )
            ->asJson(true)
            ->withContentType('application/json')
            ->asJsonResponse()
            ->put();
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP POST com Adição de envio de arquivos
     * @param string $apis
     * @param array $files
     * @param array $dados
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function postFile($api, $dados, $files = [])
    {
        $link = self::link($api);
        if (is_null($link)) {
            $res = [
                'data'      => [],
                'success'   => false,
                'message'   => "API {$api} não encontrada ou liberada para uso!",
            ];
            return response()->json($res, 404);
        }

        $url = $link . (!empty($params) ? "/{$params}" : "");

        $response = Curl::to($url)
            ->withData($dados)
            ->withHeaders(
                [
                    "content-type" => "multipart/form-data",
                ]
            )
            ->asJsonResponse();
        if (!empty($files)) {
            foreach ($files as $campo => $file) {
                $response->withFile(
                    $campo,
                    realpath($file->getPathName()),
                    $file->getType(),
                    $file->getClientOriginalName()
                );
            }
        }
        return $response->post();
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP DELETE
     * @param string $apis
     * @param array $params
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function delete($api, $params = '')
    {

        $link = self::link($api);
        if (is_null($link)) {
            $res = [
                'data'      => [],
                'success'   => false,
                'message'   => "API {$api} não encontrada ou liberada para uso!",
            ];
            return response()->json($res, 404);
        }

        $url = $link . (!empty($params) ? "/{$params}" : "");

        return Curl::to($url)
            ->withContentType('application/json')
            ->asJson()
            ->asJsonResponse()
            ->delete();
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP GET
     * um link customizado não listado nas configurações
     * @param string $link
     * @param string $params
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function to(string $link, string $params = null)
    {

        $url = $link . (!empty($params) ? "/{$params}" : "");

        return Curl::to($url)
            ->withContentType('application/json')
            ->withHeaders(
                [
                    "accept" => "*/*",
                    "accept-language" => "en-US,en;q=0.8",
                    "content-type" => "application/json",
                ]
            )
            ->asJsonResponse()
            ->get();
    }

    /**
     * Efetua consulta utilizadno VERBO HTTP GET (APIM/WSO2)
     * @param string $apis
     * @param string $token
     * @param string $params
     *
     * @return json Com resposta da API/WEBSERVICE
     */
    public function getSecurity(string $api, string $token, string $params = null)
    {
        $link = self::link($api);
        if (is_null($link)) {
            $res = [
                'data'      => [],
                'success'   => false,
                'message'   => "API {$api} não encontrada ou liberada para uso!",
            ];
            return response()->json($res, 404);
        }

        $url = $link . (!empty($params) ? "/{$params}" : "");

        return Curl::to($url)
            ->withHeaders(
                [
                    "accept" => "application/json",
                    "Authorization" => "Bearer ".$token,
                    "accept-language" => "en-US,en;q=0.8",
                ]
            )
            ->asJsonResponse()
            ->get();
    }

    /**
     * Metodo para retornar token oath2 / JWT
     *
     * @param string $api
     * @param string $clienteId
     * @param string $clienteSecret
     * @param string $grantType
     * @param string $authorization
     * @return json
     */
    public function accessToken(
        string $api,
        string $clienteId,
        string $clienteSecret,
        string $grantType       = "client_credentials",
        string $authorization   = "Basic"
    )
    {
        $this->valide($api);

        $base64 = base64_encode("{$clienteId}:{$clienteSecret}");
        return $this->api()
            ->withData([
                "grant_type" => $grantType,
            ])
            ->withContentType('application/x-www-form-urlencoded')
            ->asJsonResponse()
            ->withAuthorization("{$authorization} {$base64}")
            ->post();
    }

    public function getWithData($api, $dados)
    {
        $this->valide($api);

        return $this->api()
            ->withData($dados)
            ->asJsonResponse()
            ->get();
    }

    /**
     * @param null $api
     * @return Ixudra\Curl\Facades\Curl
     */
    public function api($api = null)
    {
        return Curl::to( is_null($api) ? $this->link : $this->link($api));
    }

    /**
     * @version 2.0
     * TODO tratar as apis lançando excpetions internas e devolvndo um json amigavel de erro
     * TODO padronizar as msgs de retorno
     */
}