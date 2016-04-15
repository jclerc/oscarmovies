<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Wit API
 */
class Wit extends Api {

    const API_BEGIN = 'https://api.wit.ai/converse?v=20160330&session_id={id}&q={message}';
    const API_CONVERSE = 'https://api.wit.ai/converse?v=20160330&session_id={id}';

    public function talk($id, $message) {

        // What we will return
        $data = [
            'entities' => [],
            'msg' => null,
            'last' => null,
            'success' => false,
        ];

        // Start conversation
        $json = $this->converse($id, $message);

        // Get entities if any
        if (isset($json->entities)) {
            foreach ($json->entities as $name => $entityArray) {
                $data['success'] = true;
                if (count($entityArray) > 0) {
                    $entity = reset($entityArray);
                    if (isset($entity->type) and isset($entity->value) and $entity->type === 'value') {
                        $data['entities'][$name] = $entity->value;
                    }
                }
            }
        }

        // We may have to merge - this is just another call to api
        if ($json and isset($json->type) and $json->type === 'merge') {
            $json = $this->converse($id);
        }

        // And retrieve msg from last response
        if ($json and isset($json->type) and $json->type === 'msg' and isset($json->msg)) {
            $data['msg'] = $json->msg;
        }

        $data['last'] = $json;

        return $data;

    }

    private function converse($id, $message = null) {
        if (isset($message)) {
            $url = $this->parseUrl(self::API_BEGIN, [
                'id' => $id,
                'message' => urlencode($message),
            ]);
        } else {        
            $url = $this->parseUrl(self::API_CONVERSE, [
                'id' => $id,
            ]);
        }

        return json_decode($this->curl($url, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                // 'Authorization: Bearer B4JZCQOKSVTGHOHKHD7ILKW7IHELLPYH',
                'Authorization: Bearer UYYN6G6CVXODMIJ2LDWT54JU6HTB3XPZ',
                'Content-Type: application/json',
                'Accept: application/json',
            ]
        ]));
    }

    // public function get($ip) {
    //     return $this->callJson('ip', ['ip' => $ip], Cache::EXPIRE_WEEK);
    // }

}
