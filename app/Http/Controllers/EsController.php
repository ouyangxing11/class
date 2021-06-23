<?php


namespace App\http\controllers;

//Es测试
class EsController
{

    public function index(){

        $hosts = [
            '127.0.0.1:9200', //IP+端口
        ];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

//        $params = [
//            'index' => 'users',
//            'body' => [
//                'settings' => [
//                    'number_of_shards' => 3,
//                    'number_of_replicas' => 2
//                ],
//                'mappings' => [
//                    '_source' => [
//                        'enabled' => true
//                    ],
//                    'properties' => [
//                        'name' => [
//                            'type' => 'keyword'
//                        ],
//                        'age' => [
//                            'type' => 'integer'
//                        ],
//                        'mobile' => [
//                            'type' => 'text'
//                        ],
//                        'email' => [
//                            'type' => 'text'
//                        ],
//                        'birthday' => [
//                            'type' => 'date'
//                        ],
//                        'address' => [
//                            'type' => 'text'
//                        ]
//                    ]
//                ]
//            ]
//        ];


// Create the index with mappings and settings now
//        $response = $client->indices()->create($params);
//        dump($response);

        //添加数据到索引
//        $params = [
//            'index' => 'users',
//            'id'    => 1,
//            'body'  => [
//                'name'     => '张三',
//                'age'      => 10,
//                'email'    => 'zs@gmail.com',
//                'birthday' => '1990-12-12',
//                'address'  => '北京'
//            ]
//        ];
//        $client->index($params);


        //批量(bulk)索引
        $arr = [
            ['name' => '张三', 'age' => 10, 'email' => 'zs@gmail.com', 'birthday' => '1990-12-12', 'address' => '北京'],
            ['name' => '李四', 'age' => 20, 'email' => 'ls@gmail.com', 'birthday' => '1990-10-15', 'address' => '河南'],
            ['name' => '白兮', 'age' => 15, 'email' => 'bx@gmail.com', 'birthday' => '1970-08-12', 'address' => '杭州'],
            ['name' => '王五', 'age' => 25, 'email' => 'ww@gmail.com', 'birthday' => '1980-12-01', 'address' => '四川'],
        ];

        foreach ($arr as $key => $document) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'users',
                    '_id'    => $key
                ]
            ];

            $params['body'][] = [
                'name'     => $document['name'],
                'age'      => $document['age'],
                'email'    => $document['email'],
                'birthday' => $document['birthday'],
                'address'  => $document['address']
            ];
        }
        if (isset($params) && !empty($params)) {
            $client->bulk($params);
        }
    }

    public function get(){
        $hosts = [
            '127.0.0.1:9200', //IP+端口
        ];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();
        $params = [
            'index' => 'users',
            'id'    => 1
        ];
        //取
        $response = $client->get($params);
        print_r($response);

//        $params = [
//            'index' => 'users',
//            'id'    => 1,
//            'body'  => [
//                'doc' => [
//                    'mobile' => '17612345678'
//                ]
//            ]
//        ];
        //更新
//        $response = $client->update($params);
//        dump($response);
    }

}