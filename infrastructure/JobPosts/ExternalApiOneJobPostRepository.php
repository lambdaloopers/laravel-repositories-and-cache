<?php

namespace Infrastructure\JobPosts;

use DateTime;
use Domain\JobPosts\Entities\JobPost;
use Domain\JobPosts\Entities\Metadata;
use Domain\JobPosts\Entities\SalaryRange;
use Domain\JobPosts\Services\JobPostRepository;
use Domain\JobPosts\Services\JobPostSearchParams;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

readonly class ExternalApiOneJobPostRepository implements JobPostRepository
{
    public function __construct(
        private Client $http
    ) {}

    public function getById(string $id): ?JobPost
    {
        try {
            $jobPostResponse = $this->http->get("job_posts_api_one/job-posts/$id");

            if ($jobPostResponse->getStatusCode() != '200') {
                return null;
            }

            $jobPostContent = json_decode($jobPostResponse->getBody()->getContents());

            return new JobPost(
                'external_api_one:'.$jobPostContent->id, // Not the most sophisticated approach
                $jobPostContent->title,
                $jobPostContent->description,
                $jobPostContent->position,
                new SalaryRange(
                    $jobPostContent->min_salary,
                    $jobPostContent->max_salary
                ),
                $jobPostContent->remote_status,
                new Metadata([
                    'industry' => $jobPostContent->industry,
                    'review_score' => $jobPostContent->review_score,
                    // ...
                ]) // Esta es la parte que da flexibilidad a nuestro modelo para poder adaptarse a extensión manteniendo un concepto de JobPost general.
            );
        } catch (GuzzleException) {
            return null;
        }
    }

    public function search(JobPostSearchParams $searchParams): array
    {
        /**
         * Imaginemos el caso en el que la API externa nos pide añadir un
         * intérvalo de fechas del JobPost, pero nosotros no usamos ese concepto.
         *
         * Tenemos que definir algún concepto razonable para cumplir con su requisito.
         *
         * Por ejemplo se me ocurre definir que nos vamos a traer los JobPosts de los últimos 12 meses
         * de la plataforma externa.
         *
         * Vamos a mirar que pasa tambien si la plataforma externa solo nos permite intervalos de mes a mes.
         *
         * La idea en este caso es mezclar todas las respuestas para que fuera de este repositorio concreto
         * no tengan efecto las limitaciones de nuestra integración.
         */
        $externalApiQueryParams = [
            // Aquí tenemos que transformar nuestros parámetros a los de la API externa.
        ];

        // Definimos los intérvalos de datos válidos para la API externa.
        $dateIntervals = [
            [
                'ini_date' => new DateTime,
                'end_date' => new DateTime,
            ],
        ];

        $jobPostContents = [];
        foreach ($dateIntervals as $dateInterval) {
            $apiQueryParams = [
                ...$externalApiQueryParams,
                'ini_date' => $dateInterval['ini_date'],
                'end_date' => $dateInterval['end_date'],
            ];

            $jobPostResponse = $this->http->get('job_posts_api_one/job-posts', $apiQueryParams);

            $jobPostContents = [
                ...$jobPostContents,
                ...json_decode($jobPostResponse->getBody()->getContents()),
            ];
        }

        $jobPosts = [];
        foreach ($jobPostContents as $jobPostContent) {
            $jobPosts[] = new JobPost(
                $jobPostContent->id,
                $jobPostContent->title,
                $jobPostContent->description,
                $jobPostContent->position,
                new SalaryRange(
                    $jobPostContent->min_salary,
                    $jobPostContent->max_salary
                ),
                $jobPostContent->remote_status,
                new Metadata($jobPostContent->metadata)
            );
        }

        return $jobPosts;
    }

    public function save(JobPost $jobPost): bool
    {
        /**
         * Queremos poder guardar JobPosts en las APIs externas?
         *
         * En caso de no ser así, estamos entrando en el terreno del CQRS que da para otra charla :)
         */

        return false;
    }
}
