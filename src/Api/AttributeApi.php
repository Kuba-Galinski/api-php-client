<?php

namespace Akeneo\Pim\ApiClient\Api;

use Akeneo\Pim\ApiClient\Client\ResourceClientInterface;
use Akeneo\Pim\ApiClient\Exception\InvalidArgumentException;
use Akeneo\Pim\ApiClient\Pagination\PageFactoryInterface;
use Akeneo\Pim\ApiClient\Pagination\PageInterface;
use Akeneo\Pim\ApiClient\Pagination\ResourceCursorFactoryInterface;
use Akeneo\Pim\ApiClient\Pagination\ResourceCursorInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Http\Promise\Promise;
use Psr\Http\Message\StreamInterface;

/**
 * API implementation to manage the attributes.
 *
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeApi implements AttributeApiInterface
{
    public const ATTRIBUTES_URI = 'api/rest/v1/attributes';
    public const ATTRIBUTE_URI = 'api/rest/v1/attributes/%s';

    public function __construct(
        protected ResourceClientInterface $resourceClient,
        protected PageFactoryInterface $pageFactory,
        protected ResourceCursorFactoryInterface $cursorFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code): array
    {
        return $this->resourceClient->getResource(static::ATTRIBUTE_URI, [$code]);
    }

    /**
     * Get attribute with table select options.
     * This method is not part of the official API.
     * It is used to get the table select options of an attribute.
     *
     * @param string $code
     *
     * @return array
     */
    public function getWithTableSelectOptions(string $code): array
    {
        return $this->resourceClient->getResource(static::ATTRIBUTE_URI, [$code], ['with_table_select_options' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function listPerPage(int $limit = 100, bool $withCount = false, array $queryParameters = []): PageInterface
    {
        $data = $this->resourceClient->getResources(static::ATTRIBUTES_URI, [], $limit, $withCount, $queryParameters);

        return $this->pageFactory->createPage($data);
    }

    /**
     * {@inheritdoc}
     */
    public function all(int $pageSize = 100, array $queryParameters = []): ResourceCursorInterface
    {
        $firstPage = $this->listPerPage($pageSize, false, $queryParameters);

        return $this->cursorFactory->createCursor($pageSize, $firstPage);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $code, array $data = []): int
    {
        if (array_key_exists('code', $data)) {
            throw new InvalidArgumentException('The parameter "code" should not be defined in the data parameter');
        }

        $data['code'] = $code;

        return $this->resourceClient->createResource(static::ATTRIBUTES_URI, [], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert(string $code, array $data = []): int
    {
        return $this->resourceClient->upsertResource(static::ATTRIBUTE_URI, [$code], $data);
    }

    public function upsertAsync(string $code, array $data = []): PromiseInterface|Promise
    {
        return $this->resourceClient->upsertAsyncResource(static::ATTRIBUTE_URI, [$code], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function upsertList($resources): \Traversable
    {
        return $this->resourceClient->upsertStreamResourceList(static::ATTRIBUTES_URI, [], $resources);
    }

    public function upsertAsyncList(StreamInterface|array $resources): PromiseInterface|Promise
    {
        return $this->resourceClient->upsertAsyncStreamResourceList(static::ATTRIBUTES_URI, [], $resources);
    }
}
