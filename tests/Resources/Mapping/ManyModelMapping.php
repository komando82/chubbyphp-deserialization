<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\ManyModel;

final class ManyModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ManyModel::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @throws DeserializerRuntimeException
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable
    {
        return function () {
            return new ManyModel();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @throws DeserializerRuntimeException
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
            DenormalizationFieldMappingBuilder::create('value')->getMapping(),
        ];
    }
}
