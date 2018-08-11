<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\DependencyInjection;

use Chubbyphp\Deserialization\DependencyInjection\DeserializationCompilerPass;
use Chubbyphp\Deserialization\Deserializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;

/**
 * @covers \Chubbyphp\Deserialization\DependencyInjection\DeserializationCompilerPass
 */
class DeserializationCompilerPassTest extends TestCase
{
    public function testProcess()
    {
        $stdClassMapping = $this->getStdClassMapping();
        $stdClassMappingClass = get_class($stdClassMapping);

        $container = new ContainerBuilder();
        $container->addCompilerPass(new DeserializationCompilerPass());

        $container
            ->register('json', JsonTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type');

        $container
            ->register('xml', XmlTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type');

        $container
            ->register('stdclass', $stdClassMappingClass)
            ->addTag('deserializer.denormalizer.objectmapping');

        $container->compile();

        self::assertTrue($container->has('chubbyphp.deserializer'));
        self::assertTrue($container->has('chubbyphp.deserializer.decoder'));
        self::assertTrue($container->has('chubbyphp.deserializer.denormalizer'));
        self::assertTrue($container->has('chubbyphp.deserializer.denormalizer.objectmappingregistry'));

        /** @var Deserializer $deserializer */
        $deserializer = $container->get('chubbyphp.deserializer');

        /** @var Decoder $decoder */
        $decoder = $container->get('chubbyphp.deserializer.decoder');

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container->get('chubbyphp.deserializer.denormalizer');

        /** @var DenormalizerObjectMappingRegistry $objectMappingRegistry */
        $objectMappingRegistry = $container->get('chubbyphp.deserializer.denormalizer.objectmappingregistry');

        self::assertInstanceOf(Deserializer::class, $deserializer);
        self::assertInstanceOf(Decoder::class, $decoder);
        self::assertInstanceOf(Denormalizer::class, $denormalizer);
        self::assertInstanceOf(DenormalizerObjectMappingRegistry::class, $objectMappingRegistry);

        self::assertSame(['key' => 'value'], $decoder->decode('{"key":"value"}', 'application/json'));

        self::assertInstanceOf(\stdClass::class, $denormalizer->denormalize(\stdClass::class, ['key' => 'value']));
    }

    private function getStdClassMapping()
    {
        return new class() implements DenormalizationObjectMappingInterface {
            /**
             * @return string
             */
            public function getClass(): string
            {
                return \stdClass::class;
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return callable
             */
            public function getDenormalizationFactory(string $path, string $type = null): callable
            {
                return function () {
                    return new \stdClass();
                };
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return DenormalizationFieldMappingInterface[]
             */
            public function getDenormalizationFieldMappings(string $path, string $type = null): array
            {
                return [];
            }
        };
    }
}
