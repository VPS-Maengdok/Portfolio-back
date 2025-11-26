<?php

namespace App\Controller;

use App\DTO\CountryDTO;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Serializer\CountrySerializer;
use App\Service\CountryService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/country', name: 'country')]
class CountryController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly CountryRepository $countryRepository,
        private readonly CountryService $countryService,
        private readonly CountrySerializer $countrySerializer
    )
    {}

    #[Route('/', name:'_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRequestService): JsonResponse
    {
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $this->countryRepository->findAllWithLocale($lang->getId());
        $serializer = $this->countrySerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Country $country, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$country) {
            throw new Exception('Company not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $isFromForm ? 
            $this->countryRepository->findOneById($country->getId()) :
            $this->countryRepository->findOneWithLocale($country->getId(), $lang->getId());
        $serializer = $this->countrySerializer->details($data, $isFromForm);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] CountryDTO $dto
    ): JsonResponse
    {
        $country = $this->countryService->create($dto);
        $serializer = $this->countrySerializer->create($country);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Country successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] CountryDTO $dto, 
        Country $country
    ): JsonResponse
    {
        if (!$country) {
            throw new Exception('Country not found.');
        }

        $countryService = $this->countryService->update($country, $dto);
        $serializer = $this->countrySerializer->update($countryService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Country successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Country $country): JsonResponse
    {
        if (!$country) {
            throw new Exception('Country not found.');
        }

        $this->countryService->delete($country);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Country successfully deleted.']);
    }
}
