<?php

use Magento\Framework\DB;
use Magento\Framework\Serialize;

return [
    'preferences' => [
        DB\LoggerInterface::class => DB\Logger\Quiet::class,
        Serialize\SerializerInterface::class => Serialize\Serializer\Json::class
    ],
    DB\Select\SelectRenderer::class => [
        'arguments' => [
            'renderers' => [
                'distinct' => [
                    'renderer' => DB\Select\DistinctRenderer::class,
                    'sort' => 100,
                    'part' => 'distinct',
                ],
                'columns' => [
                    'renderer' => DB\Select\ColumnsRenderer::class,
                    'sort' => 200,
                    'part' => 'columns',
                ],
                'union' => [
                    'renderer' => DB\Select\UnionRenderer::class,
                    'sort' => 300,
                    'part' => 'union',
                ],
                'from' => [
                    'renderer' => DB\Select\FromRenderer::class,
                    'sort' => 400,
                    'part' => 'from',
                ],
                'where' => [
                    'renderer' => DB\Select\WhereRenderer::class,
                    'sort' => 500,
                    'part' => 'where',
                ],
                'group' => [
                    'renderer' => DB\Select\GroupRenderer::class,
                    'sort' => 600,
                    'part' => 'group',
                ],
                'having' => [
                    'renderer' => DB\Select\HavingRenderer::class,
                    'sort' => 700,
                    'part' => 'having',
                ],
                'order' => [
                    'renderer' => DB\Select\OrderRenderer::class,
                    'sort' => 800,
                    'part' => 'order',
                ],
                'limitcount' => [
                    'renderer' => DB\Select\LimitRenderer::class,
                    'sort' => 900,
                    'part' => 'limitcount',
                ],
                'forupdate' => [
                    'renderer' => DB\Select\ForUpdateRenderer::class,
                    'sort' => 1000,
                    'part' => 'forupdate',
                ],
            ],
        ],
    ],
];
