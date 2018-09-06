<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
use VSV\GVQ_API\User\Repositories\UserRepository;

class CompanyJsonEnricher implements JsonEnricher
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $json
     * @return string
     * @throws \Exception
     */
    public function enrich(string $json): string
    {
        $companyAsArray = json_decode($json, true);

        $companyAsArray['id'] = $this->uuidFactory->uuid4()->toString();

        for ($index = 0; $index < count($companyAsArray['aliases']); $index++) {
            $companyAsArray['aliases'][$index]['id'] = $this->uuidFactory->uuid4()->toString();
        }

        $companyAsArray = $this->enrichWithUser($companyAsArray);

        return json_encode($companyAsArray);
    }

    /**
     * @param array $companyAsArray
     * @return array
     */
    private function enrichWithUser(array $companyAsArray): array
    {
        $user = $this->userRepository->getById(
            $this->uuidFactory->fromString($companyAsArray['user']['id'])
        );

        if ($user !== null) {
            $companyAsArray['user']['email'] = $user->getEmail()->toNative();
            $companyAsArray['user']['firstName'] = $user->getFirstName()->toNative();
            $companyAsArray['user']['lastName'] = $user->getLastName()->toNative();
            $companyAsArray['user']['role'] = $user->getRole()->toNative();
            $companyAsArray['user']['language'] = $user->getLanguage()->toNative();
            $companyAsArray['user']['active'] = $user->isActive();
        }

        return $companyAsArray;
    }
}
