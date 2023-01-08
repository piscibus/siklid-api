<?php

declare(strict_types=1);

namespace App\Tests\Foundation\Http;

use App\Foundation\Http\Request as Sut;
use App\Foundation\Util\RequestUtil;
use App\Foundation\Validation\ValidatorInterface;
use App\Tests\Concern\Util\WithJson;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor - we don't need constructor
 */
class RequestTest extends TestCase
{
    use WithJson;

    private RequestStack $requestStack;

    private RequestUtil $util;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestStack = new RequestStack();
        $this->util = new RequestUtil($this->json, $this->createMock(ValidatorInterface::class));
    }

    /**
     * @test
     */
    public function request_get_current_request(): void
    {
        $this->requestStack->push(new Request());
        $this->requestStack->push(new Request());
        $current = new Request();
        $this->requestStack->push($current);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame($current, $sut->request());
    }

    /**
     * @test
     */
    public function all_get_request_content_if_request_is_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $request->method('getContent')->willReturn('{"foo":"bar"}');
        $request->request = new InputBag();
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame(['foo' => 'bar'], $sut->all());
    }

    /**
     * @test
     */
    public function all_calls_underlying_request__all_if_request_is_not_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('html');
        $request->method('getContent')->willReturn('[]');
        $request->request = new InputBag(['foo' => 'bar']);
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame(['foo' => 'bar'], $sut->all());
    }

    /**
     * @test
     */
    public function is_json_returns_true_if_content_type_is_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertTrue($sut->isJson());
    }

    /**
     * @test
     */
    public function is_json_returns_false_if_content_type_is_not_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('html');
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertFalse($sut->isJson());
    }

    /**
     * @test
     */
    public function form_input_returns_the_same_as_all(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $request->method('getContent')->willReturn('{"foo":"bar"}');
        $request->request = new InputBag();
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame($sut->all(), $sut->formInput());
    }

    /**
     * @test
     */
    public function get_returns_get_variables(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request(['foo' => 'bar']));

        $actual = $sut->get('foo');

        $this->assertSame('bar', $actual);
    }

    /**
     * @test
     */
    public function get_returns_post_variables(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request([], ['foo' => 'bar']));

        $actual = $sut->get('foo');

        $this->assertSame('bar', $actual);
    }

    /**
     * @test
     */
    public function get_can_return_a_default_value(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request());

        $actual = $sut->get('foo', 'bar');

        $this->assertSame('bar', $actual);
    }

    /**
     * @test
     */
    public function has_checks_existence_on_query(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request(['foo' => 'bar']));

        $actual = $sut->has('foo');

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function has_checks_existence_on_post_params(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request([], ['foo' => 'bar']));

        $actual = $sut->has('foo');

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function has(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request());

        $actual = $sut->has('foo');

        $this->assertFalse($actual);
    }

    /**
     * @test
     */
    public function validate(): void
    {
        $constraint = new Assert\Collection([], null, null, true);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with([], $constraint);
        $sut = new Sut($this->requestStack, new RequestUtil($this->json, $validator));
        $this->requestStack->push(new Request());
        $sut->validate();
    }

    /**
     * @test
     */
    public function get_header(): void
    {
        $internalRequest = new Request();
        $internalRequest->headers->set('X-Foo', 'bar');
        $this->requestStack->push($internalRequest);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame('bar', $sut->getHeader('X-Foo'));
        $this->assertNull($sut->getHeader('X-Missing'));
    }
}
