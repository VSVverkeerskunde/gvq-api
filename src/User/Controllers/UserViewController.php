<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserViewController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->userRepository->getAll();

        return $this->render(
            'users/index.html.twig',
            [
                'users' => $users ? $users->toArray(): [],
            ]
        );
    }

    /**
     * @return Response
     */
    public function export(): Response
    {
        $users = $this->userRepository->getAll();
        $usersAsCsv = $this->serializer->serialize($users, 'csv');

        $usersAsCsv = $this->createCsvData($usersAsCsv);
        $response = $this->createCsvResponse($usersAsCsv);

        return $response;
    }

    /**
     * @param string $data
     * @return string
     */
    private function createCsvData(string $data): string
    {
        /**
         * @see: https://github.com/thephpleague/csv/blob/507815707cbdbebaf076873bff04cd6ad65fe0fe/docs/9.0/connections/bom.md
         */
        $csvData = chr(0xFF).chr(0xFE);
        $csvData .= mb_convert_encoding('sep=,'.PHP_EOL.$data, 'UTF-16LE', 'UTF-8');
        return $csvData;
    }

    /**
     * @param string $csvData
     * @return Response
     */
    private function createCsvResponse(string $csvData): Response
    {
        $response = new Response($csvData);

        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/csv; charset=UTF-8');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        $now = new \DateTime();
        $fileName = 'users_'.$now->format(\DateTime::ATOM).'.csv';
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );

        return $response;
    }
}
