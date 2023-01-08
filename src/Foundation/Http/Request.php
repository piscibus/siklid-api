<?php

declare(strict_types=1);

namespace App\Foundation\Http;

use App\Foundation\Util\RequestUtil;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Base request class.
 */
class Request
{
    protected RequestStack $requestStack;

    protected RequestUtil $util;

    public function __construct(RequestStack $requestStack, RequestUtil $util)
    {
        $this->requestStack = $requestStack;
        $this->util = $util;
    }

    /**
     * Get current request.
     *
     * @psalm-suppress NullableReturnStatement - we know that request is not null
     * @psalm-suppress InvalidNullableReturnType - we know that request is not null
     */
    public function request(): SymfonyRequest
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * Returns all request parameters.
     */
    public function all(): array
    {
        if ($this->isJson()) {
            return $this->util->json()->jsonToArray($this->request()->getContent());
        }

        return $this->request()->request->all();
    }

    /**
     * Checks if request is JSON.
     */
    public function isJson(): bool
    {
        return 'json' === $this->request()->getContentType();
    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function formInput(): array
    {
        return $this->all();
    }

    /**
     * Retrieves $_GET and $_POST variables respectively.
     */
    public function get(string $key, mixed $default = null): string|int|bool|null|float
    {
        assert(is_scalar($default) || is_null($default));

        $getVariables = $this->request()->query;
        $postVariables = $this->request()->request;

        if ($getVariables->has($key)) {
            assert(is_string($default) || is_null($default));

            return $getVariables->get($key, $default);
        }

        if ($postVariables->has($key)) {
            return $postVariables->get($key, $default);
        }

        return $default;
    }

    /**
     * Checks if request has a given parameter.
     */
    public function has(string $key): bool
    {
        return $this->request()->query->has($key) || $this->request()->request->has($key);
    }

    /**
     * Checks if request data is valid.
     */
    #[Required]
    public function validate(): void
    {
        $validator = $this->util->validator();
        $constraint = new Assert\Collection(
            $this->constraints(),
            null,
            null,
            $this->allowExtraFields()
        );
        $validator->validate($this->all(), $constraint);
    }

    /**
     * Returns a list of constraints to validate the request data.
     *
     * @return array<string, array<Constraint>>
     */
    protected function constraints(): array
    {
        return [];
    }

    /**
     * Determines if extra fields are allowed.
     */
    protected function allowExtraFields(): bool
    {
        return true;
    }

    /**
     * Gets a header value by name.
     */
    public function getHeader(string $key): ?string
    {
        return $this->request()->headers->get($key);
    }
}
