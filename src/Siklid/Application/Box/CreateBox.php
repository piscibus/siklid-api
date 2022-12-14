<?php

declare(strict_types=1);

namespace App\Siklid\Application\Box;

use App\Foundation\Action\AbstractAction;
use App\Foundation\Util\Hashtag;
use App\Foundation\Validation\ValidatorInterface;
use App\Siklid\Application\Box\Request\CreateBoxRequest;
use App\Siklid\Application\Contract\Entity\BoxInterface;
use App\Siklid\Document\Box;
use App\Siklid\Security\UserResolverInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

final class CreateBox extends AbstractAction
{
    private CreateBoxRequest $request;

    private DocumentManager $dm;

    private ValidatorInterface $validator;
    private Hashtag $hashtag;
    private UserResolverInterface $userResolver;

    public function __construct(
        CreateBoxRequest $request,
        DocumentManager $dm,
        ValidatorInterface $validator,
        Hashtag $hashtag,
        UserResolverInterface $userResolver
    ) {
        $this->request = $request;
        $this->dm = $dm;
        $this->validator = $validator;
        $this->hashtag = $hashtag;
        $this->userResolver = $userResolver;
    }

    public function execute(): BoxInterface
    {
        $box = $this->fill(Box::class, $this->request->formInput());
        $box->setUser($this->userResolver->getUser());
        $box->setHashtags($this->hashtag->extract((string)$box->getDescription()));

        $this->validator->validate($box);

        $this->dm->persist($box);
        $this->dm->flush();

        return $box;
    }
}
