<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class RankFeature implements SyntaxInterface
{
    private string $field;

    private ?float $boost;

    private array $functions = [
        'saturation' => [],
        // 'saturation' => ["pivot": 8],
        'log' => [
            "scaling_factor" => 4
        ],
        'sigmoid' => [],
        // 'sigmoid' => [
        //     "pivot" => 7,
        //     "exponent" => 0.6
        // ],
        'linear' => [],
    ];
    private ?array $function = null;

    public function __construct(
        string $field,
        ?float $boost = 1.0,
        ?string $function = null,
        ?array $parameters = null
    ){
        $this->field = $field;
        $this->boost = $boost;

        if ($function && array_key_exists($function, $this->functions)) {
            $functionValue = $parameters ?? $this->functions[$function];
            $this->function = [$function => $functionValue];
        }
    }

    public function build(): array
    {
        $rank_object = [
            'rank_feature' => [
                'field' => $this->field,
                'boost' => $this->boost,
            ]
        ];

        if ($this->function) {
            $functionType = array_key_first($this->function);
            $rank_object['rank_feature'][ $functionType ] = $this->function[ $functionType ];
        }

        return $rank_object;
    }
}
